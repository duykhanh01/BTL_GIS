<?php
function initDB()
{
    // Kết nối CSDL
    $paPDO = new PDO('pgsql:host=localhost;dbname=btl_gis;port=5432', 'postgres', '1');
    return $paPDO;
}
// initDB();
 $mySQLStr = "SELECT ST_AsGeoJson(geom), name from travel_location";
 //echo $mySQLStr;
 //echo "<br><br>";
 $result = query(initDB(), $mySQLStr);

//  echo '<pre>' , var_dump($result) , '</pre>';
// die();
    if(isset($_POST['functionname']))
    {
        $paPDO = initDB();
        $paSRID = '4326';
        $paPoint = $_POST['paPoint'] ?? null;
        $functionname = $_POST['functionname'];
        
        $aResult = "null";
        if ($functionname == 'getGeoCMRToAjax')
            $aResult = getGeoCMRToAjax($paPDO, $paSRID, $paPoint);
        else if ($functionname == 'getInfoCMRToAjax')
            $aResult = getInfoCMRToAjax($paPDO, $paSRID, $paPoint);
        else if($functionname == 'getInfoLocation')
            $aResult = getInfoLocation($paPDO,$paSRID,$paPoint);
        else if($functionname == 'getInfoCMRToAjax_test')
            $aResult = getInfoCMRToAjax_test($paPDO,$paSRID,$paPoint);
        else if($functionname == 'checkIn')
            $aResult = checkIn($paPDO);
        else if($functionname == 'search')
            $aResult = search($paPDO);
        else if($functionname == 'getTopFiveTravel')
            $aResult = getTopFiveTravel();
        else if($functionname == 'getTopFiveRegion')
            $aResult = getTopFiveRegion();  
        return ($aResult);
    
       // closeDB($paPDO);
    }

    function search($paPDO)
    {
        $keyword = $_POST['keyword'];
        $query = "SELECT ST_AsGeoJson(travel_location.geom) as geo, ST_AsGeoJson(gadm40_vnm_1.geom) as geo_gadm
        from \"gadm40_vnm_1\", \"travel_location\" 
        where ST_within(travel_location.geom, gadm40_vnm_1.geom)=true 
        and gadm40_vnm_1.name_1 = '$keyword'
        ";
       
      //  $query = "SELECT ST_AsGeoJson(geom) as geo from gadm40_vnm_1 where name_1 = '$keyword'";
        $result = query($paPDO, $query);
        echo json_encode($result);
       
    }


    function getTopFiveTravel()
    {
        $query = "select article.description, article.images, temp.name, temp.number_check  from article, 
        (select  check_in.travel_id, count(check_in.travel_id)
        as number_check ,travel_location.name from travel_location, check_in
        where travel_location.id = check_in.travel_id
        group by check_in.travel_id, travel_location.name order by count(check_in.travel_id) DESC) 
        as temp where article.travel_id = temp.travel_id  limit 5";
        $result = query(initDB(), $query);
        $content = '';
        foreach($result as $values)
        {
            $content .= '
            <div class="main-content d-flex w-100 mb-2 active-top">   
                <div class="main-content d-flex border rounded w-100 mb-2">
                <div class="image-thumbnail img-fluid" >
                    <a href="#">
                        <img src="'.$values['images'].'" style="width:145px; height:120px" alt="">
                    </a>
                </div>
                <div class="sub-content" style="margin-left:10px;">
                    <h3 class="title">
                        <a href="#">'.$values['name'].'</a>
                    </h3>
                    <span style="display: block;">Số người check in '.$values['number_check'].'</span>
                    <p>'.$values['description'].'</p>
                </div>
                </div>
            </div>';
        }
        echo $content;
    }

    function getTopFiveRegion()
    {
        $query = "select ga.name_1 as name, ga.avatar, sum(number_check) as number_region, ST_area(ga.geom) as area, ST_Perimeter(ga.geom) as perimeter from gadm40_vnm_1 ga, 
        (select  check_in.travel_id, count(check_in.travel_id) as number_check from travel_location, check_in where travel_location.id = check_in.travel_id
        group by check_in.travel_id order by count(check_in.travel_id)) as temp, travel_location as trv 
            where trv.id = temp.travel_id
                and ST_Within(trv.geom, ga.geom) = true
            group by ga.name_1, ga.avatar, ST_area(ga.geom), ST_Perimeter(ga.geom)
            order by number_region DESC
            limit 5";
        $result = query(initDB(), $query);
        $content = '';
        foreach($result as $values)
        {
            $content .= '
            <div class="main-content d-flex w-100 mb-2 active-top">   
                <div class="main-content d-flex border rounded w-100 mb-2">
                <div class="image-thumbnail " >
                    <a href="#">
                        <img src="'.$values['avatar'].'" class="img-fluid" style="width:145px; height:120px" alt="">
                    </a>
                </div>
                <div class="sub-content" style="margin-left:10px;">
                    <h3 class="title">
                        <a href="#">'.$values['name'].'</a>
                    </h3>
                    <span style="display: block;">Khách du lịch <span style="color: orange;">'.$values['number_region'].'</span> người</span>
                    <span style="display: block;">Chu vi <span style="color: orange;">'. round($values['perimeter'],3).'</span></span>
                    <span style="display: block;">Diện tích <span style="color: orange;">'.round($values['area'],3).'</span></span>

                </div>
                </div>
            </div>';
        }
        echo $content;
    }

    function getInfoLocation($paPDO,$paSRID,$paPoint)
    {
        $paPoint = str_replace(',', ' ', $paPoint);
       $points = "SELECT  travel_location.name, travel_location.id
        from  \"travel_location\" 
        where ST_Distance('SRID=4326;$paPoint', travel_location.geom) <= all(select ST_Distance('SRID=4326;$paPoint', travel_location.geom) from \"travel_location\") 
        and ST_Distance('SRID=4326;$paPoint', travel_location.geom) < 0.0005";
        $result = query($paPDO, $points);
        if ($result != null)
        {
            $location_id = $result[0]['id'];
            $query = "Select count(*) as count from \"check_in\" where travel_id = '$location_id'";
            $result1 =  query($paPDO, $query );
            $countCheckIn = $result1[0]['count'] ?? 0;
            $resFin = '<table>';
            // Lặp kết quả
            $resFin = $resFin."<input type='hidden' id='location_id' value='$location_id'>";
            $resFin = $resFin.'<tr><td>Tên địa điểm: '.$result[0]['name'].'</td></tr>';
            $resFin = $resFin.'<tr><td class="checkIn">Số người check in tại đây: '.$countCheckIn.'</td></tr>';
            $resFin = $resFin.'</table>';
    
            echo $resFin;
        }
        else
            return "null";
    }

    function query($paPDO, $paSQLStr)
    {
        try
        {
            // Khai báo exception
            $paPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Sử đụng Prepare 
            $stmt = $paPDO->prepare($paSQLStr);
            // Thực thi câu truy vấn
            $stmt->execute();
            
            // Khai báo fetch kiểu mảng kết hợp
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            
            // Lấy danh sách kết quả
            $paResult = $stmt->fetchAll();   
            return $paResult;                 
        }
        catch(PDOException $e) {
            echo "Thất bại, Lỗi: " . $e->getMessage();
            return null;
        }       
    }
    function closeDB($paPDO)
    {
        // Ngắt kết nối
        $paPDO = null;
    }
    function example1($paPDO)
    {
        $mySQLStr = "SELECT * FROM \"gadm40_vnm_1\"";
        $result = query($paPDO, $mySQLStr);

        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                echo $item['name_0'] . ' - '. $item['name_1'];
                echo "<br>";
            }
        }
        else
        {
            echo "example1 - null";
            echo "<br>";
        }
    }
    function example2($paPDO)
    {
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm40_vnm_1\"";
        $result = query($paPDO, $mySQLStr);
        
        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                echo $item['geo'];
                echo "<br><br>";
            }
        }
        else
        {
            echo "example2 - null";
            echo "<br>";
        }
    }
    function example3($paPDO,$paSRID,$paPoint)
    {
        echo $paPoint;
        echo "<br>";
        $paPoint = str_replace(',', ' ', $paPoint);
        echo $paPoint;
        echo "<br>";
        //$mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm40_vnm_1\" where ST_Within('SRID=4326;POINT(12 5)'::geometry,geom)";
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm40_vnm_1\" where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
        echo $mySQLStr;
        echo "<br><br>";
        $result = query($paPDO, $mySQLStr);
        
        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                echo $item['geo'];
                echo "<br><br>";
            }
        }
        else
        {
            echo "example2 - null";
            echo "<br>";
        }
    }
    function getResult($paPDO,$paSRID,$paPoint)
    {
        //echo $paPoint;
        //echo "<br>";
        $paPoint = str_replace(',', ' ', $paPoint);
        //echo $paPoint;
        //echo "<br>";
        //$mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm40_vnm_1\" where ST_Within('SRID=4326;POINT(12 5)'::geometry,geom)";
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm40_vnm_1\" where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
        //echo $mySQLStr;
        //echo "<br><br>";
        $result = query($paPDO, $mySQLStr);
        
        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                return $item['geo'];
            }
        }
        else
            return "null";
    }
    function getGeoCMRToAjax($paPDO,$paSRID,$paPoint)
    {
        //echo $paPoint;
        //echo "<br>";
        $paPoint = str_replace(',', ' ', $paPoint);
        //echo $paPoint;
        //echo "<br>";
        //$mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm40_vnm_1\" where ST_Within('SRID=4326;POINT(12 5)'::geometry,geom)";
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm40_vnm_1\" where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
        //echo $mySQLStr;
        //echo "<br><br>";
        $points = "SELECT ST_AsGeoJson(travel_location.geom) as geo, ST_AsGeoJson(gadm40_vnm_1.geom) as geo_gadm,  travel_location.name 
        from \"gadm40_vnm_1\", \"travel_location\" 
        where ST_within(travel_location.geom, gadm40_vnm_1.geom)=true 
        and
        ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,gadm40_vnm_1.geom)";
        $result = query($paPDO, $points);
      
        
        if ($result != null)
        {
            // Lặp kết quả
            // foreach ($result as $item){
            //     return $item['geo'];
            // }
        //     echo '<pre>' , var_dump($result[0]['name']) , '</pre>';
        // die();
            echo json_encode($result);
        }
        else
            return "null";
    }
    function getInfoCMRToAjax($paPDO,$paSRID,$paPoint)
    {
        //echo $paPoint;
        //echo "<br>";
        $paPoint = str_replace(',', ' ', $paPoint);
        //echo $paPoint;
        //echo "<br>";
        //$mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm40_vnm_1\" where ST_Within('SRID=4326;POINT(12 5)'::geometry,geom)";
        //$mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm40_vnm_1\" where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
        $mySQLStr = "SELECT id_1, ST_Perimeter(geom) as shape_leng, ST_Area(geom) as shape_area from \"gadm40_vnm_1\" where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
        //echo $mySQLStr;
        //echo "<br><br>";
        $result = query($paPDO, $mySQLStr);
        
        if ($result != null)
        {
            $resFin = '<table>';
            // Lặp kết quả
            foreach ($result as $item){
                $resFin = $resFin.'<tr><td>id_1: '.$item['id_1'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Chu vi: '.$item['shape_leng'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Diện tích: '.$item['shape_area'].'</td></tr>';
                break;
            }
            $resFin = $resFin.'</table>';
            return $resFin;
        }
        else
            return "null";
    }


    function checkIn($paPDO)
    {
        $location_id = $_POST['location_id'] ?? null;
        $user_id = $_POST['user_id'] ?? null;
        $queryCount = "Select count(*) as count from check_in where travel_id = '$location_id' and user_id = '$user_id'";
        $result2 = query($paPDO, $queryCount);
        $countItem = $result2[0]['count'] ?? 0;
        if($countItem==0){
            $mySQLStr = "Insert into check_in (user_id, travel_id) values('$user_id', '$location_id')";
            $result = query($paPDO, $mySQLStr);
            $query = "Select count(*) as count from check_in where travel_id = '$location_id'";
            $result1 = query($paPDO, $query);
            $count = $result1[0]['count'];
            $html = "Số người đã check in tại đây: $count";
            echo $html;
        } else{
            echo "error";
        }
     }
     function getInfoCMRToAjax_test($paPDO,$paSRID,$paPoint)
    {
        
        $paPoint = str_replace(',', ' ', $paPoint);
        
        $mySQLStr = "SELECT name_1 as tinh,name_2 as huyen, ST_Area(geom) as dientich,slbenhnhan as sl  from gadm36_vnm_2 where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
       
        $result = query($paPDO, $mySQLStr);
        
        if ($result != null)
        {

            
            $resFin = '<div>';
            // Lặp kết quả
            foreach ($result as $item){
               if ($item['sl'] <  20) {
                $resFin = $resFin.'<div style="background-color:green;padding:5px 20px;border-top-left-radius:10px;border-top-right-radius:10px;"><div>Huyện: '.$item['huyen'].'</div></div>';
                $resFin = $resFin.'<div style="background-color:green;padding:5px 20px;"><div>Tỉnh: '.$item['tinh'].'</div></div>';
                $resFin = $resFin.'<div style="background-color:green;padding:5px 20px;"><div>Diện tích: '.$item['dientich'].'</div></div>';
                $resFin = $resFin.'<div style="background-color:green;padding:5px 20px;border-bottom-left-radius:10px;border-bottom-right-radius:10px;"><div>Số lượng bệnh nhân: '.$item['sl'].'</div></div>';
               }
               else if ( $item['sl'] >= 20 &&  $item['sl'] < 50) {
                $resFin = $resFin.'<div style="background-color:yellow;padding:5px 20px;color:#000;border-top-left-radius:10px;border-top-right-radius:10px;"><div>Huyện: '.$item['huyen'].'</div></div>';
                $resFin = $resFin.'<div style="background-color:yellow;padding:5px 20px;color:#000;"><div>Tỉnh: '.$item['tinh'].'</div></div>';
                $resFin = $resFin.'<div style="background-color:yellow;padding:5px 20px;color:#000;"><div>Diện tích: '.$item['dientich'].'</div></div>';
                $resFin = $resFin.'<div style="background-color:yellow;padding:5px 20px;color:#000;border-bottom-left-radius:10px;border-bottom-right-radius:10px;"><div>Số lượng bệnh nhân: '.$item['sl'].'</div></div>';
               }
               else if ( $item['sl'] >= 50 &&  $item['sl'] < 150) {
                $resFin = $resFin.'<div style="background-color:orange;padding:5px 20px;color:#000;border-top-left-radius:10px;border-top-right-radius:10px;"><div>Huyện: '.$item['huyen'].'</div></div>';
                $resFin = $resFin.'<div style="background-color:orange;padding:5px 20px;color:#000;"><div>Tỉnh: '.$item['tinh'].'</div></div>';
                $resFin = $resFin.'<div style="background-color:orange;padding:5px 20px;color:#000;"><div>Diện tích: '.$item['dientich'].'</div></div>';
                $resFin = $resFin.'<div style="background-color:orange;padding:5px 20px;color:#000;border-bottom-left-radius:10px;border-bottom-right-radius:10px;"><div>Số lượng bệnh nhân: '.$item['sl'].'</div></div>';
               }
               else if ($item['sl'] >= 150) {
                $resFin = $resFin.'<div style="background-color:red;padding:5px 20px;border-top-left-radius:10px;border-top-right-radius:10px;"><div>Huyện: '.$item['huyen'].'</div></div>';
                $resFin = $resFin.'<div style="background-color:red;padding:5px 20px;"><div>Tỉnh: '.$item['tinh'].'</div></div>';
                $resFin = $resFin.'<div style="background-color:red;padding:5px 20px;"><div>Diện tích: '.$item['dientich'].'</div></div>';
                $resFin = $resFin.'<div style="background-color:red;padding:5px 20px;border-bottom-left-radius:10px;border-bottom-right-radius:10px;"><div>Số lượng bệnh nhân: '.$item['sl'].'</div></div>';
               
               }
               break;
            }
            $resFin = $resFin.'</div>';
            return $resFin;
        }
        else
            return "null";
    }
?>