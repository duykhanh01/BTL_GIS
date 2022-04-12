<?php

session_start();

include('config/db_connect.php');

if (!$_SESSION['email']) {
	header("Location: login.php");
}



?>
<style>
    a{
        text-decoration: none !important;
    }
    .title {
    margin: 0;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
    font-weight: bold;
    font-size: 18px;
    line-height: 26px;
    color: #333;
    
    width: 150;
    }
    .title a{
        color: #333;
    }
    .sub-content p{
        display: -webkit-box;
        -webkit-line-clamp:2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 45px;
    }
</style>
<!DOCTYPE html>
<html lang="en">

<?php include('templates/header.php'); ?>


    <div class=" w-100 mt-3 container" style="position: relative;">
        
       <div class="row">
       <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 " id="map" onclick="popup();" style="height: 100vh;"></div>
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" >
                <p class="btn btn-top-travel" style="color:red; margin-bottom:0px;">Top 5 địa điểm yêu thích</p>
                <div class="article-top">

                </div>
                <p class="btn btn-top-region" style="color:red; margin-bottom:0px;">Top 5 tỉnh nhiều người tới thăm nhất</p>
                <div class="region-top">

                </div>
                <p class="btn btn-show-more" style="color:red; margin-bottom:0px;">Hiển thị tất cả</p>
                
        </div>
       </div>

    </div>
   
        <div class=" modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Chi tiết</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                   <div class="content">

                                   </div>
                                   <button type="button" class="btn btn-info mt-3 btn-checkin">Check in</button>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary " data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
        </div>
        <div aria-live="polite" aria-atomic="true" style="position: relative; min-height: 200px;">
        </div>
        <!-- Position it -->
        <div class ="toast-wrapper d-none" style="position: absolute; top: 0; right: 0;">      
            <!-- Then put toasts within -->
            <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Thông báo</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
              
            </div>
            </div>
        </div>
        <?php include 'CMR_pgsqlAPI.php' ?>
        
        <?php
            //$myPDO = initDB();
            //$mySRID = '4326';
            //$pointFormat = 'POINT(12,5)';

            //example1($myPDO);
            //example2($myPDO);
            //example3($myPDO,'4326','POINT(12,5)');
            //$result = getResult($myPDO,$mySRID,$pointFormat);

            //closeDB($myPDO);
        ?>
        <script>
           
            var format = 'image/png';
            var map;
            var minX = 5.49874900000009;
            var minY = 1;
            var maxX = 205.1921150000001;
            var maxY = 30.0780600000001;
            var cenX = (minX + maxX) / 2;
            var cenY = (minY + maxY) / 2;
            var mapLat = cenY;
            var mapLng = cenX;
            var mapDefaultZoom = 6;
            function initialize_map() {
				// thêm

		// const features = [];		
		// function creatPoint(points)
		// {
			
		// 	for (i = 0; i < length(points); i++) {
		// 		features.push(new ol.Feature({
		// 			geometry: new ol.geom.Point(ol.proj.fromLonLat([
		// 				points.x	, points.y
		// 		]))
		// 		}));
      	// 	}
		// }		
      
		const getRandomNumber = function (min, ref) {
        return Math.random() * ref + min;
      }
      const features = [];
    //   features.push(new ol.Feature({
    //         geometry: new ol.geom.Point(ol.proj.fromLonLat([
    //             105.61516,21.39584
    //         ]))
    //         }));
    
    function setFeatures(points)
    {
       
        for (i = 0; i < points.length; i++) {
            features.push(new ol.Feature({
            geometry: new ol.geom.Point(ol.proj.fromLonLat([
                JSON.parse(points[i]['geo']).coordinates[0]  ,  JSON.parse(points[i]['geo']).coordinates[1]
            ]))
            }));
           //console.log(JSON.parse(points[i]['geo']).coordinates[0]);
        }
        
        setSourceMap();

      }
      function setSourceMap()
      {
        vectorSource.refresh();
        vectorSource = new ol.source.Vector({
            features
        });
        vectorLayer_1.setSource(vectorSource);
       
      }
      
     
      // create the source and layer for random features
      let vectorSource = new ol.source.Vector({
        features
      });
  
      let vectorLayer_1 = new ol.layer.Vector({
        source: vectorSource,
        style: new ol.style.Style({
          image: new ol.style.Circle({
            radius: 4,
            fill: new ol.style.Fill({color: 'red'})
          })
        })
      });

				// end thêm
                //*
                layerBG = new ol.layer.Tile({
                    source: new ol.source.OSM({})
                });
                //*/
                let map_name = 'travel_location_test';
                $('.change-map').click(function(){
                    map_name =  ($(this).attr('id'));
                })
                var layerCMR_adm1 = new ol.layer.Image({
                    source: new ol.source.ImageWMS({
                        ratio: 1,
                        url: 'http://localhost:8080/geoserver/btl_gis/wms?',
                        params: {
                            'FORMAT': format,
                            'VERSION': '1.1.1',
                            STYLES: '',
                            LAYERS: map_name,
                        }
                    })
                });

                var viewMap = new ol.View({
                    center: ol.proj.fromLonLat([mapLng, mapLat]),
                    zoom: mapDefaultZoom
                    //projection: projection
                });
           
                map = new ol.Map({
                    target: "map",
                  //  layers: [layerBG, vectorLayer_1],
                    layers: [layerBG],
                    view: viewMap
                });
                //map.getView().fit(bounds, map.getSize());
                let show_more_location = 0;
                $('.btn-show-more').click(function(){
                    if(show_more_location==0)
                    {
                        map.addLayer(layerCMR_adm1);
                        show_more_location = 1;
                        $('.btn-show-more').text('Ẩn tất cả');
                    }                    
                    else
                    {
                        map.removeLayer(layerCMR_adm1);
                        show_more_location = 0;
                        $('.btn-show-more').text('Hiển thị tất cả');
                    }
                        
                })
                var styles = {
                    'MultiPolygon': new ol.style.Style({
                        
                        stroke: new ol.style.Stroke({
                            color: 'green', 
                            width: 3
                        })
                    })
                };
                var styleFunction = function (feature) {
                    return styles[feature.getGeometry().getType()];
                };
                var vectorLayer = new ol.layer.Vector({
                    //source: vectorSource,
                    style: styleFunction
                });
                map.addLayer(vectorLayer);

                function createJsonObj(result) {                    
                    var geojsonObject = '{'
                            + '"type": "FeatureCollection",'
                            + '"crs": {'
                                + '"type": "name",'
                                + '"properties": {'
                                    + '"name": "EPSG:4326"'
                                + '}'
                            + '},'
                            + '"features": [{'
                                + '"type": "Feature",'
                                + '"geometry": ' + result
                            + '}]'
                        + '}';
                    return geojsonObject;
                }
                function drawGeoJsonObj(paObjJson) {
                    var vectorSource = new ol.source.Vector({
                        features: (new ol.format.GeoJSON()).readFeatures(paObjJson, {
                            dataProjection: 'EPSG:4326',
                            featureProjection: 'EPSG:3857'
                        })
                    });
                    var vectorLayer = new ol.layer.Vector({
                        source: vectorSource
                    });
                    map.addLayer(vectorLayer);
                }
                function highLightGeoJsonObj(paObjJson) {
                    var vectorSource = new ol.source.Vector({
                        features: (new ol.format.GeoJSON()).readFeatures(paObjJson, {
                            dataProjection: 'EPSG:4326',
                            featureProjection: 'EPSG:3857'
                        })
                    });
					vectorLayer.setSource(vectorSource);
                    /*
                    var vectorLayer = new ol.layer.Vector({
                        source: vectorSource
                    });
                    map.addLayer(vectorLayer);
                    */
                }
                function highLightObj(result) {
                    //alert("result: " + result);
                    var strObjJson = createJsonObj(result);
                    //alert(strObjJson);
                    var objJson = JSON.parse(strObjJson);
                    //alert(JSON.stringify(objJson));
                    //drawGeoJsonObj(objJson);
                    highLightGeoJsonObj(objJson);
                }
                map.on('singleclick', function (evt) {
                    //alert("coordinate: " + evt.coordinate);
                    //var myPoint = 'POINT(12,5)';
                    var lonlat = ol.proj.transform(evt.coordinate, 'EPSG:3857', 'EPSG:4326');
                    var lon = lonlat[0];
                    var lat = lonlat[1];
                    var myPoint = 'POINT(' + lon + ' ' + lat + ')';
                    //alert("myPoint: " + myPoint);
                    //*
                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        dataType: 'json',
                        data: {functionname: 'getGeoCMRToAjax', paPoint: myPoint},
                
                        success : function (result, status, erro) {
							points =(result);
                            vectorSource.clear();
                            setFeatures(points);

                            highLightObj(points[0]['geo_gadm']);
                           
                        },
                       
                    });
                });
                map.on('click', function (evt) {
                    //alert("coordinate: " + evt.coordinate);
                    //var myPoint = 'POINT(12,5)';
                    var lonlat = ol.proj.transform(evt.coordinate, 'EPSG:3857', 'EPSG:4326');
                    var lon = lonlat[0];
                    var lat = lonlat[1];
                    var myPoint = 'POINT(' + lon + ' ' + lat + ')';
                  
                  //  alert("myPoint: " + myPoint);
                    //*
                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {functionname: 'getInfoLocation', paPoint: myPoint},
                
                        success : function (result, status, erro) {
                              var myModal = new bootstrap.Modal(document.getElementById('exampleModal'), {
                                keyboard: true
                                })
                                console.log(result);
                                if(result!='')
                                {
                                    myModal.show();
                                
                                    $('.modal-body .content').html(result);
                                }
                                
                          //  console.log(1);
                        },
                        error: function (req, status, error) {
                            console.log(req + " " + status + " " + error);
                        }
                    });
                    //*/
                });
                $('.btn-checkin').click(function(){
                    let location_id = $('#location_id').val();
                    let user_id = <?php echo $_SESSION['id'] ?>;
                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {functionname: 'checkIn', 'location_id': location_id, 'user_id': user_id},
                        success : function (result, status, erro) {
                            if(result=="error"){
                                $('.toast-wrapper').removeClass('d-none')
                                $('.toast-body').html("Bạn đã check in ở đây rồi!");
                                $('.toast').toast('show');
                                $('.btn-checkin').attr('disabled','disabled');
                            } else{
                                $('.toast-wrapper').removeClass('d-none')
                                $('.btn-checkin').removeAttr("disabled")
                                $('.checkIn').html(result);
                                $('.toast').toast('show')
                                $('.toast-body').html("Bạn đã check in thành công");
                            }
                        },
                        errors : function (result, status, erro) {
                            alert("Loi");
                        }
                       
                    });
                })
                $('.btn-search').click(function(e){
                    let keyword = $('#search-location').val();
                    e.preventDefault;
                    alert(keyword);
                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        dataType: 'json',
                        data: {functionname: 'search', 'keyword': keyword},
                        success : function (result, status, erro) {
                            console.log(result);
                            setFeatures(result);
                            highLightObj((result[0]['geo_gadm']));
                        },
                        error: function (req, status, error) {
                            console.log(req + " " + status + " " + error);
                        }
                       
                    });
                })
                let show_travel_top = 0;
                let show_region_top = 0;
                $('.btn-top-travel').on('click', function (evt) {
                    
                    
                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {functionname: 'getTopFiveTravel'},
                
                        success : function (result, status, erro) {
                            $('.article-top').html(result);
                            $('btn-top-travel').children().hasClass('active-top')
                            {
                                if(show_travel_top ==0 )
                                {
                                    $('.article-top').addClass('d-none');
                                    show_travel_top = 1;
                                }
                                else
                                {
                                    $('.article-top').removeClass('d-none');
                                    show_travel_top = 0 ;
                                }
                            }
                        },
                        error: function (req, status, error) {
                            console.log(req + " " + status + " " + error);
                        }
                    });
                    //*/
                });
                $('.btn-top-region').on('click', function (evt) {
                    
                    
                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {functionname: 'getTopFiveRegion'},
                
                        success : function (result, status, erro) {
                            $('.region-top').html(result);
                            $('btn-top-region').children().hasClass('active-top')
                            {
                                if(show_region_top ==0 )
                                {
                                    $('.region-top').addClass('d-none');
                                    show_region_top = 1;
                                }
                                else
                                {
                                    $('.region-top').removeClass('d-none');
                                    show_region_top = 0 ;
                                }
                            }
                        },
                        error: function (req, status, error) {
                            console.log(req + " " + status + " " + error);
                        }
                    });
                    //*/
                });
            };
            // test
            function displayObjInfo_test(result)
            {
            alert("result: " + result);
            //alert("coordinate des: " + coordinate);
            $("#info").html(result);
           
            }
            map.on('singleclick', function () {
                alert(123);
            var lonlat = ol.proj.transform( 'EPSG:3857', 'EPSG:4326');
            var lon = lonlat[0];
            var lat = lonlat[1];
            var myPoint = 'POINT(' + lon + ' ' + lat + ')';
            //alert("myPoint: " + myPoint);
               
            $.ajax({
                type: "POST",
                url: "API.php",
                data: {functionname: 'getInfoCMRToAjax_test', paPoint: myPoint},
                success : function (result, status, erro) {
                    displayObjInfo_test(result );
                },
                error: function (req, status, error) {
                    alert(req + " " + status + " " + error);
                }
            });
            //*/
        });
        
            // end test
        </script>


<?php include('templates/footer.php'); ?>


</html>