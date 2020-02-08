<?php

/* /vote.twig */
class __TwigTemplate_2e0ab4cde78a92b7b2a8b85350f16c515d5d1a5007d076a69359e0abc2c1d94b extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<html lang=\"en\">

<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\">
    <title>Document</title>

    <link rel=\"stylesheet\" href=\"http://127.0.0.1:8000/assets/css/bootstrap.min.css\">
    <link rel=\"stylesheet\" href=\"http://127.0.0.1:8000/assets/css/shards.min.css\">
    <link rel=\"stylesheet\" href=\"http://127.0.0.1:8000/assets/css/shards-extras.min.css\">
    <link rel=\"stylesheet\" href=\"http://127.0.0.1:8000/assets/css/style.css\">

</head>

<body>
    <div id=\"main-content\">
        <div class=\"card vote\">
            <h5 class=\"card-header\">";
        // line 20
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["subject"]) || array_key_exists("subject", $context) ? $context["subject"] : (function () { throw new Twig_Error_Runtime('Variable "subject" does not exist.', 20, $this->source); })()), "name", array()), "html", null, true);
        echo "</h5>
            <div class=\"card-body vote\">
                <div class=\"description\">
                    ";
        // line 23
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["subject"]) || array_key_exists("subject", $context) ? $context["subject"] : (function () { throw new Twig_Error_Runtime('Variable "subject" does not exist.', 23, $this->source); })()), "description", array()), "html", null, true);
        echo "
                </div>
                <form action=\"http://127.0.0.1:8000/vote/\" method=\"POST\">
                    <div class=\"form-group\">
                        <label for=\"univ\">Select your university</label>
                        <select name=\"univ\" id=\"vote-univ\" class=\"form-control\" required>
                            ";
        // line 29
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["univs"]) || array_key_exists("univs", $context) ? $context["univs"] : (function () { throw new Twig_Error_Runtime('Variable "univs" does not exist.', 29, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["univ"]) {
            // line 30
            echo "                                <option value=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["univ"], "id", array()), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["univ"], "name", array()), "html", null, true);
            echo "</option>
                            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['univ'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 32
        echo "                        </select>
                    </div>

                    <div class=\"form-group\">
                        <label for=\"fac\">Select your faculty</label>
                        <select name=\"fac\" id=\"vote-fac\" class=\"form-control\" disabled required>
                        </select>
                    </div>

                    <div class=\"form-group\">
                        <label for=\"dep\">Select your department</label>
                        <select name=\"dep\" id=\"vote-dep\" class=\"form-control\" disabled required>
                        </select>
                    </div>
                    <div class=\"form-group\">
                        <label for=\"year\">At what year are you</label>
                        <input type=\"number\" name=\"year\" id=\"year\" class=\"form-control\" placeholder=\"20..\"
                            aria-describedby=\"helpId\" required>
                    </div>

                    <div class=\"form-group\">
                        <label for=\"options\">Select an option</label>
                        <select name=\"option\" id=\"vote-options\" class=\"form-control\" required>
                            ";
        // line 55
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["opts"]) || array_key_exists("opts", $context) ? $context["opts"] : (function () { throw new Twig_Error_Runtime('Variable "opts" does not exist.', 55, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["opt"]) {
            // line 56
            echo "                                <option value=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["opt"], "id", array()), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["opt"], "name", array()), "html", null, true);
            echo "</option>
                            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['opt'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 58
        echo "                        </select>
                    </div>

                    <div class=\"form-group\">
                        <button type=\"submit\" class=\"btn btn-primary\">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src=\"http://127.0.0.1:8000/assets/js/jquery.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/popper.min.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/bootstrap.min.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/shards.min.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/main.js\"></script>
</body>

</html>";
    }

    public function getTemplateName()
    {
        return "/vote.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  114 => 58,  103 => 56,  99 => 55,  74 => 32,  63 => 30,  59 => 29,  50 => 23,  44 => 20,  23 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("<!DOCTYPE html>
<html lang=\"en\">

<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\">
    <title>Document</title>

    <link rel=\"stylesheet\" href=\"http://127.0.0.1:8000/assets/css/bootstrap.min.css\">
    <link rel=\"stylesheet\" href=\"http://127.0.0.1:8000/assets/css/shards.min.css\">
    <link rel=\"stylesheet\" href=\"http://127.0.0.1:8000/assets/css/shards-extras.min.css\">
    <link rel=\"stylesheet\" href=\"http://127.0.0.1:8000/assets/css/style.css\">

</head>

<body>
    <div id=\"main-content\">
        <div class=\"card vote\">
            <h5 class=\"card-header\">{{subject.name}}</h5>
            <div class=\"card-body vote\">
                <div class=\"description\">
                    {{subject.description}}
                </div>
                <form action=\"http://127.0.0.1:8000/vote/\" method=\"POST\">
                    <div class=\"form-group\">
                        <label for=\"univ\">Select your university</label>
                        <select name=\"univ\" id=\"vote-univ\" class=\"form-control\" required>
                            {%for univ in univs%}
                                <option value=\"{{univ.id}}\">{{univ.name}}</option>
                            {%endfor%}
                        </select>
                    </div>

                    <div class=\"form-group\">
                        <label for=\"fac\">Select your faculty</label>
                        <select name=\"fac\" id=\"vote-fac\" class=\"form-control\" disabled required>
                        </select>
                    </div>

                    <div class=\"form-group\">
                        <label for=\"dep\">Select your department</label>
                        <select name=\"dep\" id=\"vote-dep\" class=\"form-control\" disabled required>
                        </select>
                    </div>
                    <div class=\"form-group\">
                        <label for=\"year\">At what year are you</label>
                        <input type=\"number\" name=\"year\" id=\"year\" class=\"form-control\" placeholder=\"20..\"
                            aria-describedby=\"helpId\" required>
                    </div>

                    <div class=\"form-group\">
                        <label for=\"options\">Select an option</label>
                        <select name=\"option\" id=\"vote-options\" class=\"form-control\" required>
                            {%for opt in opts%}
                                <option value=\"{{opt.id}}\">{{opt.name}}</option>
                            {%endfor%}
                        </select>
                    </div>

                    <div class=\"form-group\">
                        <button type=\"submit\" class=\"btn btn-primary\">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src=\"http://127.0.0.1:8000/assets/js/jquery.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/popper.min.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/bootstrap.min.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/shards.min.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/main.js\"></script>
</body>

</html>", "/vote.twig", "/var/www/html/vote/resources/views/vote.twig");
    }
}
