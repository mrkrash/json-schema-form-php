<?php

namespace JsonSchemaForm;

use StdClass;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

class Generator
{
    public StdClass $schema;
    public ?Environment $twig;
    public ?StdClass $data;
    public array $errors;
    public array $errorPaths;

    /**
     * @param StdClass $schema
     * @param StdClass|null $data - The data to enter in the form
     * @param array|null $errors - Any errors to highlight and apply in the form
     */
    public function __construct(StdClass $schema, StdClass $data = null, array $errors = null)
    {
        $this->schema = $schema;
        $this->data = $data;

        $this->errors = (empty($errors) ? array() : $errors);
        //easy and fast lookup
        $this->errorPaths = array_column($this->errors, 'property');
    }

    /**
     * @param Environment $twig
     */
    public function setTwig(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function getDefaultTwigEnvironment(): Environment
    {
        return new Environment(new FilesystemLoader(realpath(dirname(__FILE__).'/../../templates')));
    }

    /**
     * @param string $template - Template filename
     * @param array $formRenderOptions - Any additional options per element for rendering
     * e.g. array('my.path' => array('inputType' => 'textarea'))
     * @return string	HTML form
     * @throws SyntaxError|LoaderError|RuntimeError
     */
    public function render(string $template = 'form.twig', array $formRenderOptions = array()): string
    {
        if (!($this->twig instanceof Environment)) {
            $this->setTwig($this->getDefaultTwigEnvironment());
        }

        //update valid with schema $formRenderOptions config
        if (!empty($formRenderOptions)) {
            foreach ($formRenderOptions as $path => $config) {
                $schemaNode = JsonPath::getSchemaNode($path, $this->schema);
                if ($schemaNode) {
                    foreach ($config as $key => $value) {
                        $schemaNode->{$key} = $value;
                    }
                }
            }
        };

        $fieldGeneratorClassName =  'JsonSchemaForm\\ChunkGenerator\\' . ucfirst($this->schema->type) . 'Field';
        $fieldGenerator = new $fieldGeneratorClassName(
            $this,
            (empty($formRenderOptions['path']) ?
                array() :
                $formRenderOptions['path'])
        );
        return $this->twig->render($template, array(
            'html' => $fieldGenerator->render()
        ));
    }
}
