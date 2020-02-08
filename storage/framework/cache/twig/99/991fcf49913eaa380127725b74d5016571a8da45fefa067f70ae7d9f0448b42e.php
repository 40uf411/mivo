<?php

/* /index.twig */
class __TwigTemplate_bb6e8f18fac98645a93387ce45e89a0d755d8c11021e67665e392bd261f4f812 extends Twig_Template
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
        <div class=\"card\">
            <h5 class=\"card-header\">لاحة المواضيع</h5>
            <div class=\"card-body\">
                ";
        // line 20
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["subjects"]) || array_key_exists("subjects", $context) ? $context["subjects"] : (function () { throw new Twig_Error_Runtime('Variable "subjects" does not exist.', 20, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["subject"]) {
            // line 21
            echo "                    <a href=\"/vote/";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["subject"], "id", array()), "html", null, true);
            echo "\" class=\"subject-link\">";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["subject"], "name", array()), "html", null, true);
            echo "</a>
                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['subject'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 23
        echo "                
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
        return "/index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  59 => 23,  48 => 21,  44 => 20,  23 => 1,);
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
        <div class=\"card\">
            <h5 class=\"card-header\">لاحة المواضيع</h5>
            <div class=\"card-body\">
                {%for subject in subjects%}
                    <a href=\"/vote/{{subject.id}}\" class=\"subject-link\">{{subject.name}}</a>
                {%endfor%}
                
            </div>
          </div>
    </div>

    <script src=\"http://127.0.0.1:8000/assets/js/jquery.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/popper.min.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/bootstrap.min.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/shards.min.js\"></script>
    <script src=\"http://127.0.0.1:8000/assets/js/main.js\"></script>
</body>
</html>", "/index.twig", "/var/www/html/vote/resources/views/index.twig");
    }
}
