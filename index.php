<?php

session_start();

include('config/db_connect.php');

if (!$_SESSION['email']) {
	header("Location: login.php");
}



?>

<!DOCTYPE html>
<html lang="en">

<?php include('templates/header.php'); ?>


    <div class="d-flex w-100 mt-3">
    
        <div id="map" onclick="popup();" style="width: 70%; height: 100vh;"></div>
        
      
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
            var minX = 8.49874900000009;
            var minY = 1.65254800000014;
            var maxX = 16.1921150000001;
            var maxY = 13.0780600000001;
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
                let map_name = 'travel_location_2';
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
                    layers: [layerBG, vectorLayer_1],
                    //layers: [layerCMR_adm1],
                    view: viewMap
                });
                //map.getView().fit(bounds, map.getSize());
                
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
                            $('.checkIn').html(result);

                            alert("Bạn đã check in thành công");
                        },

                       
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
            };
        </script>


<?php include('templates/footer.php'); ?>


</html>