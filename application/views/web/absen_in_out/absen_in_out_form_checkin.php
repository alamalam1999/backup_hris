<!-- Main Content -->
<div class="main-content">
  <section class="section">
    <div class="card shadow">

      <div id="camera" class="row col-md-12 mb-4">
        <?php if ($status_device == 'mobile') { ?>
          <div id="box_camera" style="margin-left: -70px; padding-left: 0px; left: 0px;">
            <div id="my_camera"></div>
          </div>
          <input type=button value="Take Snapshot" onClick="take_snapshot()" class="btn btn-info col-md-10 mt-4 ml-4">
        <?php } else { ?>
          <div id="box_camera" style="margin: auto; width: 50%;">
            <div id="my_camera"></div>
            <input type=button value="Take Snapshot" onClick="take_snapshot()" class="btn btn-info col-md-12 text-center">
          </div>
        <?php } ?>
      </div>
      <div id="body" class="card-body p-0">

        <?php 
        // $this->load->view("web/Absen_in_out/Absen_in_out_map"); 
        ?>



        <style media="screen">
          /* Always set the map height explicitly to define the size of the div
                     * element that contains the map. */
          /* #map {
                height: 100%;
                } */

          /* Optional: Makes the sample page fill the window. */
          #gmapxx {
            height: 100%;
            height: 320px;
            margin: 0;
            padding: 0;
            background: #ccc;
          }

          .custom-map-control-button {
            background-color: #fff;
            border: 0;
            border-radius: 2px;
            box-shadow: 0 1px 4px -1px rgba(0, 0, 0, 0.3);
            margin: 10px;
            padding: 0 0.5em;
            font: 400 18px Roboto, Arial, sans-serif;
            overflow: hidden;
            height: 40px;
            cursor: pointer;
          }

          .custom-map-control-button:hover {
            background: #ebebeb;
          }

          /* #my_camera{
                    width: 500px;
                    height: 240px;
                    border: 1px solid black;
                } */
        </style>

        <!-- <div id="note"><span id="title">Inside the circle?</span><hr />
              <span class="info">Marker <strong>A</strong>: <span id="a" class="bool"></span></span>
            </div> -->
        <div id="gmapxx"></div>
        <?php
        // echo "<pre>";
        // print_r($data->DATE); die();

        ?>




        <div class="card card-danger">
          <div class="row mb-2 mt-4">
            <div class="col-md-12 col-sm-12">
              <label class="col-md-12 col-form-label pr-0 pt-0 text-center">
                <h5><strong>TIME ATTENDANCE</strong></h5>
              </label>
            </div>
            <div class="col-md-12 col-sm-12" style="margin-top: -15px;">
              <label class="col-md-12 col-form-label pr-0 pt-0 text-center">
                <h6><?= tgl($data->DATE) ?></h6>
              </label>
            </div>
          </div>

          <form id="form" action="<?= site_url("absen_in_out/proses_add") ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" id="STATUS_KOORDINAT" value="">
            <input type="hidden" name="TIPE" id="TIPE" value="<?= $type ?>" class="form-control">
            <input type="hidden" id="STATUS_CAMSHOT" value="">
            <input type="hidden" name="LONGITUDE" id="LONGITUDE" value="">
            <input type="hidden" name="LATITUDE" id="LATITUDE" value="">
            <input type="hidden" name="TGL_JADWAL" value="<?= $data->DATE ?>">
            <input type="hidden" name="PERIODE_ID" value="<?= $data->PERIODE_ID ?>">

            <div id="detail_status" class="row col-md-12 m-2 ">
              <div class="row">

                <div class="col-md-3 col-sm-12">
                  <div class="row text-center">

                    <btn class="col-md-12 mr-2 mb-2"><i class="fas fa-check-double text-info" style="font-size: 35px;"></i>

                      <?php
                      if ($type == 'checkin') $status_absen = 'CHECK IN';
                      if ($type == 'checkout') $status_absen = 'CHECK OUT';
                      ?>
                      <span class="text-success ml-2" style="font-size: 20px;"><strong><?= $status_absen ?></strong></span>

                    </btn>
                  </div>
                </div>

                <div class="col-md-3 col-sm-12">
                  <div class="row text-center">

                    <btn class="col-md-12 mr-2 mb-2"><i class="fas fa-globe-asia text-info" style="font-size: 35px;"></i>
                      <span class="text-success ml-2" style="font-size: 15px;"><strong id="text_status_koordinat"></strong></span>
                    </btn>
                  </div>
                </div>

                <div class="col-md-3 col-sm-12">
                  <div class="row text-center">

                    <btn class="col-md-12 mr-2 mb-2"><i class="fas fa-map-marker-alt text-info" style="font-size: 35px;"></i>
                      <span class="text-success ml-2" style="font-size: 20px;"><strong id="text_lat"></strong></span>
                    </btn>
                  </div>
                </div>
                <div class="col-md-3 col-sm-12">
                  <div class="row text-center">

                    <btn class="col-md-12 mr-2 mb-2"><i class="fas fa-map-marker text-info" style="font-size: 35px;"></i>
                      <span class="text-success ml-2" style="font-size: 20px;"><strong id="text_long"></strong></span>
                    </btn>
                  </div>
                </div>





              </div>
            </div>

            <?php
            $lebar_hasil = '10px';
            ?>
            <div class="row mb-2 mt-0">
              <div class="row col-md-12">
                <?php if ($status_device == 'mobile') { ?>

                  <div id="box_hasil_camera" style="margin: auto; width: 50%;">
                    <div id="results"></div>
                    <!-- <input type=button value="Take Snapshot again" id="btn_show" class=" btn btn-info col-md-12 text-center" style="display: none;" onClick="show_snapshot()"> -->
                    <input type="hidden" name="image" class="image-tag">
                  </div>
                <?php } else { ?>
                  <div id="box_hasil_camera" style="margin: auto; width: 50%;">
                    <div id="results"></div>
                    <!-- <input type=button value="Take Snapshot again" id="btn_show" class=" btn btn-info col-md-12 text-center" style="display: none;" onClick="show_snapshot()"> -->
                    <input type="hidden" name="image" class="image-tag">
                  </div>
                <?php } ?>
              </div>

              <div class="row col-md-12 text-center mt-4">
                <div class="col-md-12">
                  <!-- <h5 class="text-success pt-2">Tekan Simpan untuk melakukan absen</h5> -->

                  <button id="btn_save" type="submit" class="btn btn-success col-md-5 ml-3"><i class="fa fa-save"></i> <?= $status_absen ?></button>
                  <div class="col-md-12 col-sm-12" id="not_valid">
                    <label class="col-md-12 col-form-label pr-0 pt-0 text-center text-danger">
                      <h3><strong>Lokasi Anda tidak Valid, silahkan lakukan absen di tempat yang telah ditentukan</strong></h3>
                    </label>
                  </div>
                </div>
              </div>

            </div>

          </form>

        </div>


      </div>

    </div>
  </section>
</div>


<!-- Async script executes immediately and must be after any DOM elements used in callback. -->
<!-- <script type="text/javascript">

  var keys= "AIzaSyBPVAauCFvRN16LnxNo2Qo2cAURi-sLJuU";
</script> -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBPVAauCFvRN16LnxNo2Qo2cAURi-sLJuU&callback=initMap&v=weekly&channel=2" async></script>


<!-- <script type="text/javascript">

let map;

function initMap() {
map = new google.maps.Map(document.getElementById("map"), {
  center: { lat: -34.397, lng: 150.644 },
  zoom: 8,
});
}

</script> -->
<script src="<?php echo base_url() ?>assets/modules/jquery/dist/jquery.min.js"></script>
<script src="<?php echo base_url() ?>assets/get_camera/webcamjs/webcam.js"></script>

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script> -->

<script type="text/javascript">
  $(document).ready(function() {
    $(".custom-map-control-button").trigger("click");
    $("#body").hide();
    $("#btn_save").hide();
    $("#detail_status").hide();


  });
</script>
<script type="text/javascript">
  let map, infoWindow;



  function initMap() {

    map = new google.maps.Map(document.getElementById("gmapxx"), {
      //-6.249928430399072, 106.77843675415102
      //-6.179603206403208, 106.7736808401347


      /* center: {
        lat: <?php /* echo $data->LATITUDE; */ ?>,
        lng: <?php /* echo $data->LONGITUDE; */ ?>
      }, */

      zoom: 16,
    });
    infoWindow = new google.maps.InfoWindow();


    // Try HTML5 geolocation.
    if (navigator.geolocation) {



      const locationButton = document.createElement("button");


      locationButton.classList.add("custom-map-control-button");
      map.controls[google.maps.ControlPosition.TOP_CENTER].push(locationButton);

      navigator.geolocation.getCurrentPosition(
        (position) => {
          const pos = {
            lat: position.coords.latitude,
            lng: position.coords.longitude,
          };

          locationButton.textContent = "Lokasi Anda  Long: " + pos.lat + " ,Lang : " + pos.lng;
          //locationButton.textContent = "Verifikasi Lokasi anda " ;




          //SET MAKER A
          var latLngA = new google.maps.LatLng(pos.lat, pos.lng),
            markerA = new google.maps.Marker({
              position: latLngA,
              title: 'Location',
              map: map,
              animation: google.maps.Animation.BOUNCE,
              draggable: false
            });

          var val_lat = <?php echo $data->LATITUDE; ?>;
          var val_long = <?php echo $data->LONGITUDE; ?>;

          //BIKIN Marker  Cycle
          var
            //latLngCMarker = new google.maps.LatLng(-6.260276026752639, 106.78265759538183),
            //posisi Kantor

            latLngCMarker = new google.maps.LatLng(val_lat, val_long),

            markerCenter = new google.maps.Marker({
              position: latLngCMarker,
              title: 'Location',
              map: map,
              draggable: false
            }),
            infoCenter = new google.maps.InfoWindow({
              content: contentCenter
            }),


            circle = new google.maps.Circle({
              map: map,
              clickable: false,
              // metres
              radius: 1000,
              fillColor: '#fff',
              fillOpacity: .6,
              strokeColor: '#313131',
              strokeOpacity: .4,
              strokeWeight: .8
            });
          // Bikin Cycle nya
          circle.bindTo('center', markerCenter, 'position');

          //SET JIKA KLIK => ZOOM
          // map.addListener("center_changed", () => {
          //   window.setTimeout(() => {
          //     map.panTo(markerA.getPosition());
          //   }, 3000);
          // });
          markerA.addListener("click", () => {
            map.setZoom(16);
            map.setCenter(markerA.getPosition());
          });

          //CEK LOKASI ANDA
          //JIKA KLIK
          status = "NOT VALID";
          bounds = circle.getBounds();
          //var bounds.union(circle.getBounds());
          var latLngA = new google.maps.LatLng(pos.lat, pos.lng);
          //alert(latLngA);

          if (bounds.contains(latLngA)) {
            status = "LOCATION VALID";
            $("#detail_status").show();

            $("#not_valid").hide();
            $("#btn_save").show();
            $('#LONGITUDE').val(pos.lng);
            $('#LATITUDE').val(pos.lat);
          } else {
            $("#not_valid").show();
            $("#btn_save").hide();
            $('#LONGITUDE').val("");
            $('#LATITUDE').val("");
          }

          infoWindow.setPosition(pos);
          //infoWindow.setContent("Location found long: " + pos.lat +" ,Lang : "+ pos.lng);
          infoWindow.setContent("Location found status: " + status + " ,Lat : " + pos.lat + " ,Long : " + pos.lng);
          infoWindow.open(map);
          map.setCenter(pos);


          $('#STATUS_KOORDINAT').val(status);
          $('#text_status_koordinat').text(status);
          $('#text_lat').text(pos.lat);
          $('#text_long').text(pos.lng);



          // locationButton.addEventListener("click", () => {

          //  take_snapshot();

          // });


        },
        () => {
          handleLocationError(true, infoWindow, map.getCenter());
        }
      );
    } else {
      // Browser doesn't support Geolocation
      handleLocationError(false, infoWindow, map.getCenter());
    }
    //




    //tambahan agung

    var
      contentCenter = '<span class="infowin">Center Marker (draggable)</span>',
      contentA = '<span class="infowin">Marker A (draggable)</span>';


    infoWindow.setContent("Location found long: XXX");
    infoWindow.open(map);
    //map.setCenter(pos);


  } //batas initmap



  function handleLocationError(browserHasGeolocation, infoWindow, pos) {
    infoWindow.setPosition(pos);
    infoWindow.setContent(
      browserHasGeolocation ?
      "Error: The Geolocation service failed." :
      "Error: Your browser doesn't support geolocation."
    );
    infoWindow.open(map);
  }

  function cek_lokasi(pos, map, latLngA) {

  }
</script>


<!-- Configure a few settings and attach camera -->
<script language="JavaScript">
  Webcam.set({
    width: 490,
    height: 390,
    image_format: 'jpeg',
    jpeg_quality: 90
  });

  Webcam.attach('#my_camera');

  function take_snapshot() {

    $("#camera").hide();
    $("#body").show();


    Webcam.snap(function(data_uri) {
      $(".image-tag").val(data_uri);
      <?php if ($status_device == 'mobile') { ?>

        document.getElementById('results').innerHTML = '<img src="' + data_uri + '" style="width:200px;"/>';

      <?php } else { ?>

        document.getElementById('results').innerHTML = '<img src="' + data_uri + '"/>';


      <?php } ?>


    });

    $("#STATUS_CAMSHOT").val("SUKSES");
    $("#box_camera").hide();
    $("#btn_show").show();
    $("#box_hasil_camera").show();
    $("#results").show();
    Webcam.reset('#my_camera')


  }

  function show_snapshot() {
    $("#box_camera").show();
    $("#btn_show").hide();
    $("#results").hide();
    $("#box_hasil_camera").hide();
    $("#STATUS_CAMSHOT").val("");
    Webcam.attach('#my_camera');
  }
</script>