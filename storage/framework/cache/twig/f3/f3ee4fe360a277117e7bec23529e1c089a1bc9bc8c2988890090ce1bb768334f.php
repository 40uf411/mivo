<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* /index.twig */
class __TwigTemplate_f41b3209e2cbe661dc50b9cf81538ac44ade60230628056ea143c43f10f43b42 extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<!DOCTYPE html>
<html lang=\"en\">

<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\">
    <title>MIVO</title>

    <link rel=\"stylesheet\" href=\"http://127.0.0.1:8000/assets/css/bootstrap.min.css\">
    <link rel=\"stylesheet\" href=\"http://127.0.0.1:8000/assets/css/shards.min.css\">
    <link rel=\"stylesheet\" href=\"http://127.0.0.1:8000/assets/css/shards-extras.min.css\">
    <link rel=\"stylesheet\" href=\"http://127.0.0.1:8000/assets/css/lightslider.min.css\">
    <link rel=\"stylesheet\" href=\"http://127.0.0.1:8000/assets/css/fonts.css\">
    <link rel=\"stylesheet\" href=\"http://127.0.0.1:8000/assets/css/icofont.min.css\">
    <link rel=\"stylesheet\" href=\"http://127.0.0.1:8000/assets/css/style.css\">
    <link rel=\"icon\" href=\"http://127.0.0.1:8000/favicon.ico\">

</head>

<body>
    <div id=\"the-whole\">
        <nav class=\"navbar navbar-expand-sm\">
            <a class=\"navbar-brand\" href=\"#\">MIVO</a>
            <button class=\"navbar-toggler d-lg-none\" type=\"button\" data-toggle=\"collapse\"
                data-target=\"#collapsibleNavId\" aria-controls=\"collapsibleNavId\" aria-expanded=\"false\"
                aria-label=\"Toggle navigation\"> <img src=\"http://127.0.0.1:8000/assets/img/menu.svg\" height=\"36px\"
                    alt=\"\"> </button>
            <form class=\"form-inline my-2 my-lg-0\">
                <input class=\"form-control mr-sm-2\" type=\"text\" placeholder=\"Search\" id=\"global-search\">
            </form>
            <div class=\"collapse navbar-collapse\" id=\"collapsibleNavId\">
                <ul class=\"navbar-nav mr-auto mt-2 mt-lg-0\" id=\"nav-items\">
                    <li class=\"nav-item dropdown\">
                        <a class=\"nav-link dropdown-toggle\" href=\"#\" id=\"login\" data-toggle=\"dropdown\"
                            aria-haspopup=\"true\" aria-expanded=\"false\"> Log-in </a>
                        <div class=\"dropdown-menu p-3\" aria-labelledby=\"login\">
                            <form id=\"login-form\" method=\"POST\">
                                <div class=\"text-center text-danger\" id=\"login-error-box\">

                                </div>
                                <div class=\"form-group\">
                                    <label for=\"email\">Email address</label>
                                    <input type=\"email\" class=\"form-control\" id=\"email\" aria-describedby=\"emailHelp\"
                                        placeholder=\"Enter email\" name=\"email\">
                                </div>
                                <div class=\"form-group\">
                                    <label for=\"pass\">Password or Pin</label>
                                    <input type=\"password\" class=\"form-control\" id=\"pass\" placeholder=\"Password\"
                                        name=\"password\">
                                </div>
                                <button class=\"btn btn-primary text-white\">Submit</button>
                            </form>
                        </div>
                    </li>
                    <li class=\"nav-item home active\">
                        <a class=\"nav-link\" href=\"#\">Home</a>
                    </li>
                </ul>
            </div>
        </nav>
        <div class=\"container-fluid  main-container\">
            <div class=\"row justify-content-md-center main-row\">
                <div id=\"profile-side\" class=\"\"></div>
                <!------------>
                <div id=\"content-side\" class=\"col align-self-start\">
                </div>
            </div>
        </div>

        <div class=\"mic-running-countdown text-left\" data-seconds-left=5>
        </div>
        <ul id=\"movie-rightclick-menu\" class=\"dropdown-menu np-context-menu np-popup rightclick-menu\" role=\"menu\"
            style=\"display: none; position: absolute;\">
            <li class=\"details-button\" tabindex=\"-1\">Show more details</li>
            <li class=\"divider\"></li>
            <li class=\"similar-button\" tabindex=\"-1\">Find similar movies</li>
        </ul>
        <ul id=\"user-rightclick-menu\" class=\"dropdown-menu np-context-menu np-popup rightclick-menu\" role=\"menu\"
            style=\"display: none; position: absolute;\">
            <li class=\"profile-button\" tabindex=\"-1\">Show profile</li>
            <li class=\"follow-button\" tabindex=\"-1\">Follow/Unfollow</li>
            <li class=\"divider\"></li>
            <li class=\"block-button\" tabindex=\"-1\">Block</li>
        </ul>
        <div id=\"mic-record-for-emo\">
            <img src=\"http://127.0.0.1:8000/assets/img/microphone.svg\" alt=\"\">
        </div>
    </div>
    <div style=\"display:none\">
        <button id=\"btn-start-recording\">Start Recording</button>
        <button id=\"btn-stop-recording\" >Stop Recording</button>
        <button id=\"btn-release-microphone\" >Release Microphone</button>
        <button id=\"btn-download-recording\" >Download</button>
    
        <hr>
        <div><audio controls autoplay playsinline></audio></div>
    </div>
    <!-- notification -->
    <div id=\"notification\">
    </div>
    <!-- Modal -->
    <div class=\"modal fade\" id=\"movie-details-model\" tabindex=\"1000\" role=\"dialog\"
        aria-labelledby=\"exampleModalLongTitle\" aria-hidden=\"true\">
        <div class=\"modal-dialog\" role=\"document\">
        </div>
    </div>
    <div id=\"model-close-button\">
        <img src=\"http://127.0.0.1:8000/assets/img/cancel.svg\" alt=\"\">
    </div>

    <script src=\"http://127.0.0.1:8000/assets/js/jquery.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/jquery.simple.timer.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/popper.min.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/bootstrap.min.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/shards.min.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/lightslider.min.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/jquery.npContextMenu.js\"></script>

    <script src=\"http://127.0.0.1:8000/assets/js/handlebars-v4.1.2.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/typeahead.bundle.min.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/typeahead.jquery.min.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/bloodhound.min.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/chart.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/RecordRTC.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/main.js\"></script>

    <script type=\"text/javascript\">
        \$(document).ready(function () {
            \$(\"#slider\").css(\"max-width\", \$(\"#content-side\").width());
            \$(window).on('resize', function () {
                \$(\"#slider\").css(\"max-width\", \$(\"#content-side\").width());
            });
            \$(\"#lightSlider\").lightSlider({
                rtl: true,
                adaptiveHeight: true,
                item: 2,
                loop: false,
                easing: 'cubic-bezier(0.25, 0, 0.25, 1)',
                speed: 600,
                responsive: [{
                        breakpoint: 1100,
                        settings: {
                            item: 3,
                            slideMove: 1,
                            slideMargin: 6,
                        }
                    },
                    {
                        breakpoint: 800,
                        settings: {
                            item: 2,
                            slideMove: 1
                        }
                    }
                ]
            });
        });
    </script>

</body>

</html>";
    }

    public function getTemplateName()
    {
        return "/index.twig";
    }

    public function getDebugInfo()
    {
        return array (  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<!DOCTYPE html>
<html lang=\"en\">

<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\">
    <title>MIVO</title>

    <link rel=\"stylesheet\" href=\"http://127.0.0.1:8000/assets/css/bootstrap.min.css\">
    <link rel=\"stylesheet\" href=\"http://127.0.0.1:8000/assets/css/shards.min.css\">
    <link rel=\"stylesheet\" href=\"http://127.0.0.1:8000/assets/css/shards-extras.min.css\">
    <link rel=\"stylesheet\" href=\"http://127.0.0.1:8000/assets/css/lightslider.min.css\">
    <link rel=\"stylesheet\" href=\"http://127.0.0.1:8000/assets/css/fonts.css\">
    <link rel=\"stylesheet\" href=\"http://127.0.0.1:8000/assets/css/icofont.min.css\">
    <link rel=\"stylesheet\" href=\"http://127.0.0.1:8000/assets/css/style.css\">
    <link rel=\"icon\" href=\"http://127.0.0.1:8000/favicon.ico\">

</head>

<body>
    <div id=\"the-whole\">
        <nav class=\"navbar navbar-expand-sm\">
            <a class=\"navbar-brand\" href=\"#\">MIVO</a>
            <button class=\"navbar-toggler d-lg-none\" type=\"button\" data-toggle=\"collapse\"
                data-target=\"#collapsibleNavId\" aria-controls=\"collapsibleNavId\" aria-expanded=\"false\"
                aria-label=\"Toggle navigation\"> <img src=\"http://127.0.0.1:8000/assets/img/menu.svg\" height=\"36px\"
                    alt=\"\"> </button>
            <form class=\"form-inline my-2 my-lg-0\">
                <input class=\"form-control mr-sm-2\" type=\"text\" placeholder=\"Search\" id=\"global-search\">
            </form>
            <div class=\"collapse navbar-collapse\" id=\"collapsibleNavId\">
                <ul class=\"navbar-nav mr-auto mt-2 mt-lg-0\" id=\"nav-items\">
                    <li class=\"nav-item dropdown\">
                        <a class=\"nav-link dropdown-toggle\" href=\"#\" id=\"login\" data-toggle=\"dropdown\"
                            aria-haspopup=\"true\" aria-expanded=\"false\"> Log-in </a>
                        <div class=\"dropdown-menu p-3\" aria-labelledby=\"login\">
                            <form id=\"login-form\" method=\"POST\">
                                <div class=\"text-center text-danger\" id=\"login-error-box\">

                                </div>
                                <div class=\"form-group\">
                                    <label for=\"email\">Email address</label>
                                    <input type=\"email\" class=\"form-control\" id=\"email\" aria-describedby=\"emailHelp\"
                                        placeholder=\"Enter email\" name=\"email\">
                                </div>
                                <div class=\"form-group\">
                                    <label for=\"pass\">Password or Pin</label>
                                    <input type=\"password\" class=\"form-control\" id=\"pass\" placeholder=\"Password\"
                                        name=\"password\">
                                </div>
                                <button class=\"btn btn-primary text-white\">Submit</button>
                            </form>
                        </div>
                    </li>
                    <li class=\"nav-item home active\">
                        <a class=\"nav-link\" href=\"#\">Home</a>
                    </li>
                </ul>
            </div>
        </nav>
        <div class=\"container-fluid  main-container\">
            <div class=\"row justify-content-md-center main-row\">
                <div id=\"profile-side\" class=\"\"></div>
                <!------------>
                <div id=\"content-side\" class=\"col align-self-start\">
                </div>
            </div>
        </div>

        <div class=\"mic-running-countdown text-left\" data-seconds-left=5>
        </div>
        <ul id=\"movie-rightclick-menu\" class=\"dropdown-menu np-context-menu np-popup rightclick-menu\" role=\"menu\"
            style=\"display: none; position: absolute;\">
            <li class=\"details-button\" tabindex=\"-1\">Show more details</li>
            <li class=\"divider\"></li>
            <li class=\"similar-button\" tabindex=\"-1\">Find similar movies</li>
        </ul>
        <ul id=\"user-rightclick-menu\" class=\"dropdown-menu np-context-menu np-popup rightclick-menu\" role=\"menu\"
            style=\"display: none; position: absolute;\">
            <li class=\"profile-button\" tabindex=\"-1\">Show profile</li>
            <li class=\"follow-button\" tabindex=\"-1\">Follow/Unfollow</li>
            <li class=\"divider\"></li>
            <li class=\"block-button\" tabindex=\"-1\">Block</li>
        </ul>
        <div id=\"mic-record-for-emo\">
            <img src=\"http://127.0.0.1:8000/assets/img/microphone.svg\" alt=\"\">
        </div>
    </div>
    <div style=\"display:none\">
        <button id=\"btn-start-recording\">Start Recording</button>
        <button id=\"btn-stop-recording\" >Stop Recording</button>
        <button id=\"btn-release-microphone\" >Release Microphone</button>
        <button id=\"btn-download-recording\" >Download</button>
    
        <hr>
        <div><audio controls autoplay playsinline></audio></div>
    </div>
    <!-- notification -->
    <div id=\"notification\">
    </div>
    <!-- Modal -->
    <div class=\"modal fade\" id=\"movie-details-model\" tabindex=\"1000\" role=\"dialog\"
        aria-labelledby=\"exampleModalLongTitle\" aria-hidden=\"true\">
        <div class=\"modal-dialog\" role=\"document\">
        </div>
    </div>
    <div id=\"model-close-button\">
        <img src=\"http://127.0.0.1:8000/assets/img/cancel.svg\" alt=\"\">
    </div>

    <script src=\"http://127.0.0.1:8000/assets/js/jquery.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/jquery.simple.timer.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/popper.min.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/bootstrap.min.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/shards.min.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/lightslider.min.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/jquery.npContextMenu.js\"></script>

    <script src=\"http://127.0.0.1:8000/assets/js/handlebars-v4.1.2.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/typeahead.bundle.min.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/typeahead.jquery.min.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/bloodhound.min.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/chart.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/RecordRTC.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/main.js\"></script>

    <script type=\"text/javascript\">
        \$(document).ready(function () {
            \$(\"#slider\").css(\"max-width\", \$(\"#content-side\").width());
            \$(window).on('resize', function () {
                \$(\"#slider\").css(\"max-width\", \$(\"#content-side\").width());
            });
            \$(\"#lightSlider\").lightSlider({
                rtl: true,
                adaptiveHeight: true,
                item: 2,
                loop: false,
                easing: 'cubic-bezier(0.25, 0, 0.25, 1)',
                speed: 600,
                responsive: [{
                        breakpoint: 1100,
                        settings: {
                            item: 3,
                            slideMove: 1,
                            slideMargin: 6,
                        }
                    },
                    {
                        breakpoint: 800,
                        settings: {
                            item: 2,
                            slideMove: 1
                        }
                    }
                ]
            });
        });
    </script>

</body>

</html>", "/index.twig", "/var/www/html/mivo/resources/views/index.twig");
    }
}
