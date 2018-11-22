<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="Dashboard">
  <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
  <title>Dashio - Bootstrap Admin Template</title>

  <!-- Favicons -->
  <link href="template/img/favicon.png" rel="icon">
  <link href="template/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Bootstrap core CSS -->
  <link href="template/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!--external css-->
  <link href="template/lib/font-awesome/css/font-awesome.css" rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href="template/css/zabuto_calendar.css">
  <link rel="stylesheet" type="text/css" href="template/lib/gritter/css/jquery.gritter.css" />
  <!-- Custom styles for this template -->
  <link href="template/css/style.css" rel="stylesheet">
  <link href="template/css/style-responsive.css" rel="stylesheet">
  <script src="template/lib/chart-master/Chart.js"></script>
  <style>
    h3 {
      font-weight: 300 !important;
      font-size: 17px;
    }
    .pn {
      height: 200px;
    }
  </style>
</head>

<body>
  <section id="container">
    <!-- **********************************************************************************************************************************************************
        TOP BAR CONTENT & NOTIFICATIONS
        *********************************************************************************************************************************************************** -->
    <!--header start-->
    <header class="header black-bg">
      <div class="sidebar-toggle-box">
        <div class="fa fa-bars tooltips" data-placement="right" data-original-title="Toggle Navigation"></div>
      </div>
      <!--logo start-->
      <a href="index.html" class="logo"><b>DASH<span>IO</span></b></a>
      <!--logo end-->
      <div class="top-menu">
        <ul class="nav pull-right top-menu">
          <li><a class="logout" href="login.html">Logout</a></li>
        </ul>
      </div>
    </header>
    <!--header end-->
    <!-- **********************************************************************************************************************************************************
        MAIN SIDEBAR MENU
        *********************************************************************************************************************************************************** -->
    <!--sidebar start-->
    <aside>
      <div id="sidebar" class="nav-collapse ">
        <!-- sidebar menu start-->
        <ul class="sidebar-menu" id="nav-accordion">
          <p class="centered"><a href="profile.html"><img src="template/img/ui-sam.jpg" class="img-circle" width="80"></a></p>
          <li>
            <a href="index.php?node=50">
              <i class="fa fa-envelope"></i>
              <span>50 </span>
              </a>
          </li>
          <li>
            <a href="index.php?node=100">
              <i class="fa fa-envelope"></i>
              <span>100 </span>
              </a>
          </li>
          <li>
            <a href="index.php?node=200">
              <i class="fa fa-envelope"></i>
              <span>200 </span>
              </a>
          </li>
          <li>
            <a href="index.php?node=300">
              <i class="fa fa-envelope"></i>
              <span>300 </span>
              </a>
          </li>
          <li>
            <a href="index.php?node=400">
              <i class="fa fa-envelope"></i>
              <span>400 </span>
              </a>
          </li>
        </ul>
        <!-- sidebar menu end-->
      </div>
    </aside>
    <!--sidebar end-->
    <!-- **********************************************************************************************************************************************************
        MAIN CONTENT
        *********************************************************************************************************************************************************** -->
    <!--main content start-->
    <section id="main-content">
      <section class="wrapper">
        <div class="row">
          <div class="col-lg-12 main-chart">
            <!--CUSTOM CHART START -->
            <div class="border-head">
              <h3>TEST</h3>
            </div>
            <div class="custom-bar-chart">
              <ul class="y-axis">
                <li><span>100</span></li>
                <li><span>80</span></li>
                <li><span>60</span></li>
                <li><span>40</span></li>
                <li><span>20</span></li>
                <li><span>0</span></li>
              </ul>
              <div class="bar">
                <div class="title">HEFT</div>
                <div id="heft" class="value tooltips" data-original-title="8.500" data-toggle="tooltip" data-placement="top">15%</div>
              </div>
              <div class="bar ">
                <div class="title">SA without CT</div>
                <div id="saw" class="value tooltips" data-original-title="6.000" data-toggle="tooltip" data-placement="top">15%</div>
              </div>
              <div class="bar ">
                <div class="title">SA with CT</div>
                <div id="sa" class="value tooltips" data-original-title="4.500" data-toggle="tooltip" data-placement="top">15%</div>
              </div>
              <div class="bar ">
                <div class="title">Equal CT</div>
                <div id="equalCt" class="value tooltips" data-original-title="7.500" data-toggle="tooltip" data-placement="top">15%</div>
              </div>
              <div class="bar ">
                <div class="title">Equal Heft</div>
                <div id="equalHeft" class="value tooltips" data-original-title="7.500" data-toggle="tooltip" data-placement="top">15%</div>
              </div>
              <div class="bar ">
                <div class="title">All Equal</div>
                <div id="allEqual" class="value tooltips" data-original-title="7.500" data-toggle="tooltip" data-placement="top">15%</div>
              </div>
            </div>
          </div>
          <!-- /col-lg-9 END SECTION MIDDLE -->
        </div>
        <div class="row">
          <div class="col-md-4 col-sm-6 col-lg-2">
            <div class="weather-3 pn centered">
              <i class="fa fa-star"></i>
              <h1 class="heft">0</h1>
              <div class="info">
                <div class="row">
                  <h3 class="centered">HEFT</h3>
                </div>
              </div>
            </div>
          </div> 
          <div class="col-md-4 col-sm-6 col-lg-2">
            <div class="weather-3 pn centered">
              <i class="fa fa-star"></i>
              <h1 class="sa">0</h1>
              <div class="info">
                <div class="row">
                  <h3 class="centered">SA with CT input</h3>
                </div>
              </div>
            </div>
          </div> 
          <div class="col-md-4 col-sm-6 col-lg-2">
            <div class="weather-3 pn centered">
              <i class="fa fa-star"></i>
              <h1 class="saw">0</h1>
              <div class="info">
                <div class="row">
                  <h3 class="centered">SA without CT input</h3>
                </div>
              </div>
            </div>
          </div> 
          <div class="col-md-4 col-sm-6 col-lg-2">
            <div class="weather-3 pn centered">
              <i class="fa fa-star"></i>
              <h1 class="equalCt">0</h1>
              <div class="info">
                <div class="row">
                  <h3 class="centered">equal with CT</h3>
                </div>
              </div>
            </div>
          </div> 
          <div class="col-md-4 col-sm-6 col-lg-2">
            <div class="weather-3 pn centered">
              <i class="fa fa-star"></i>
              <h1 class="equalHeft">0</h1>
              <div class="info">
                <div class="row">
                  <h3 class="centered">Equal with HEFT</h3>
                </div>
              </div>
            </div>
          </div>  
          <div class="col-md-4 col-sm-6 col-lg-2">
            <div class="weather-3 pn centered">
              <i class="fa fa-star"></i>
              <h1 class="allEqual">0</h1>
              <div class="info">
                <div class="row">
                  <h3 class="centered">All equall</h3>
                </div>
              </div>
            </div>
          </div>    
        </div>
        <div class="row" style="margin-top: 10px;">
          <div class="col-md-3 col-sm-3 mb">
            <div class="green-panel pn">
              <div class="green-header">
                <h5>HEFT</h5>
              </div>
              <div class="chart mt">
                <div class="sparkline" data-type="line" data-resize="true" data-height="75" data-width="90%" data-line-width="1" data-line-color="#fff" data-spot-color="#fff" data-fill-color="" data-highlight-line-color="#fff" data-spot-radius="4" data-data="[200,135,667,333,526,996,564,123,890,464,655]"><canvas style="display: inline-block; width: 296px; height: 75px; vertical-align: top;" width="296" height="75"></canvas></div>
              </div>
              <p class="mt mheft">0</p>
            </div>
          </div>
          <div class="col-md-3 col-sm-3 mb">
            <!-- REVENUE PANEL -->
            <div class="green-panel pn">
              <div class="green-header">
                <h5>CT</h5>
              </div>
              <div class="chart mt">
                <div class="sparkline" data-type="line" data-resize="true" data-height="75" data-width="90%" data-line-width="1" data-line-color="#fff" data-spot-color="#fff" data-fill-color="" data-highlight-line-color="#fff" data-spot-radius="4" data-data="[200,135,667,333,526,996,564,123,890,464,655]"><canvas style="display: inline-block; width: 296px; height: 75px; vertical-align: top;" width="296" height="75"></canvas></div>
              </div>
              <p class="mt mct">0</p>
            </div>
          </div>
          <div class="col-md-3 col-sm-3 mb">
            <!-- REVENUE PANEL -->
            <div class="green-panel pn">
              <div class="green-header">
                <h5>SA with ct</h5>
              </div>
              <div class="chart mt">
                <div class="sparkline" data-type="line" data-resize="true" data-height="75" data-width="90%" data-line-width="1" data-line-color="#fff" data-spot-color="#fff" data-fill-color="" data-highlight-line-color="#fff" data-spot-radius="4" data-data="[200,135,667,333,526,996,564,123,890,464,655]"><canvas style="display: inline-block; width: 296px; height: 75px; vertical-align: top;" width="296" height="75"></canvas></div>
              </div>
              <p class="mt msa">0</p>
            </div>
          </div>
          <div class="col-md-3 col-sm-3 mb">
            <!-- REVENUE PANEL -->
            <div class="green-panel pn">
              <div class="green-header">
                <h5>SA withouut CT</h5>
              </div>
              <div class="chart mt">
                <div class="sparkline" data-type="line" data-resize="true" data-height="75" data-width="90%" data-line-width="1" data-line-color="#fff" data-spot-color="#fff" data-fill-color="" data-highlight-line-color="#fff" data-spot-radius="4" data-data="[200,135,667,333,526,996,564,123,890,464,655]"><canvas style="display: inline-block; width: 296px; height: 75px; vertical-align: top;" width="296" height="75"></canvas></div>
              </div>
              <p class="mt msaw">0</p>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12 mt">
            <div class="content-panel">
              <table class="table table-hover">
                <h4><i class="fa fa-angle-right"></i>آزمایشات</h4>
                <hr>
                <thead>
                  <tr>
                    <th>#</th>
                    <th>HEFT</th>
                    <th>Cross threshold</th>
                    <th>SA without CT</th>
                    <th>SA with CT</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
          <!-- /col-md-12 -->
        </div>
        <!-- /row -->
      </section>
    </section>
    <!--main content end-->
    <!--footer start-->
    <footer class="site-footer">
      <div class="text-center">
        <p>
          &copy; Copyrights <strong>Dashio</strong>. All Rights Reserved
        </p>
        <div class="credits">
          <!--
            You are NOT allowed to delete the credit link to TemplateMag with free version.
            You can delete the credit link only if you bought the pro version.
            Buy the pro version with working PHP/AJAX contact form: https://templatemag.com/dashio-bootstrap-admin-template/
            Licensing information: https://templatemag.com/license/
          -->
          Created with Dashio template by <a href="https://templatemag.com/">TemplateMag</a>
        </div>
        <a href="index.html#" class="go-top">
          <i class="fa fa-angle-up"></i>
          </a>
      </div>
    </footer>
    <!--footer end-->
  </section>
  <!-- js placed at the end of the document so the pages load faster -->
  <script src="template/lib/jquery/jquery.min.js"></script>

  <script src="template/lib/bootstrap/js/bootstrap.min.js"></script>
  <script class="include" type="text/javascript" src="template/lib/jquery.dcjqaccordion.2.7.js"></script>
  <script src="template/lib/jquery.scrollTo.min.js"></script>
  <script src="template/lib/jquery.nicescroll.js" type="text/javascript"></script>
  <script src="template/lib/jquery.sparkline.js"></script>
  <!--common script for all pages-->
  <script src="template/lib/common-scripts.js"></script>
  <script type="text/javascript" src="template/lib/gritter/js/jquery.gritter.js"></script>
  <script type="text/javascript" src="template/lib/gritter-conf.js"></script>
  <!--script for this page-->
  <script src="template/lib/sparkline-chart.js"></script>
  <script src="template/lib/zabuto_calendar.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      $("#date-popover").popover({
        html: true,
        trigger: "manual"
      });
      $("#date-popover").hide();
      $("#date-popover").click(function(e) {
        $(this).hide();
      });

      $("#my-calendar").zabuto_calendar({
        action: function() {
          return myDateFunction(this.id, false);
        },
        action_nav: function() {
          return myNavFunction(this.id);
        },
        ajax: {
          url: "show_data.php?action=1",
          modal: true
        },
        legend: [{
            type: "text",
            label: "Special event",
            badge: "00"
          },
          {
            type: "block",
            label: "Regular event",
          }
        ]
      });
    });

    function myNavFunction(id) {
      $("#date-popover").hide();
      var nav = $("#" + id).data("navigation");
      var to = $("#" + id).data("to");
      console.log('nav ' + nav + ' to: ' + to.month + '/' + to.year);
    }

    $(function(){
        var counter = 1;
        var permission = 1;
        var total = {
            equalHeft: 0,
            equalCt: 0,
            allEqual: 0,
            heft : 0,
            ct : 0,
            sa : 0,
            saw: 0
        };
        var makespan = {
            heft : 0,
            ct : 0,
            sa : 0,
            saw: 0
        };
    
        getRandomOne();
        
        $("#stop").on("click",function(){
            permission = 0;
        });
        
        function getRandomOne()
        {
            jQuery.ajax({
                url: "run.php?node="+<?php echo $_GET["node"] ?>,
                success: function (result) {
                  result = JSON.parse(result);
                  best = "sa";
                  val = result.sa
                  
                  if (result.sa == result.heft) {
                    best = "equalHeft";
                    val = result.heft;
                  }
                  if (result.ct == result.sa) {
                    best = "equalCt";
                    val = result.ct;
                  }
                  if (result.ct == result.sa && result.sa == result.heft) {
                    best = "allEqual";
                    val = result.ct;
                  }
                  if(result.heft < val){
                      best = "heft";
                      val  = result.heft;
                  }
                  if(result.ct < val){
                      best = "ct";
                      val  = result.ct;
                  }
                  if (result.saw < val) {
                    best = "saw";
                    val = result.saw;
                  }
                  if (result.sa < val) {
                    best = "sa";
                    val = result.sa;
                  }
                    
                  total[best]++;
                  
                  content = "<tr><td>"+counter+"<td>"+result.heft+"</td>"
                            +"<td>"+result.ct+"</td>"
                            + "<td>" + result.saw + "</td>"
                            + "<td>" + result.sa + "</td>"
                            +"<td>"+best+"("+val+")</td><tr>";
                  $("tbody").append(content);

                  makespan["heft"] += result.heft;
                  makespan["saw"]  += result.saw;
                  makespan["ct"]   += result.ct;
                  makespan["sa"]   += result.sa;

                  max = 0;
                  for (method in total) {
                    max += total[method];
                  }
                  for (var key in total) {
                    $("#"+key).height(((total[key] / max)*100)+"%");
                    $("#"+key).attr("data-original-title",total[key]);
                    $("."+key).text(total[key]);
                    $(".m"+key).text(makespan[key]);
                  }
                },
                error: function() {
                  console.error("runRequests Error");
                },
                complete: function() {
                  if(counter < 750){
                    counter++;
                    getRandomOne();
                  }
                }
            });
        }
    });
  </script>
</body>

</html>
