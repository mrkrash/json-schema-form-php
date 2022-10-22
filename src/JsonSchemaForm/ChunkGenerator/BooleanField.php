<?php

namespace JsonSchemaForm\ChunkGenerator;

use JsonSchemaForm\ChunkGenerator;

class BooleanField extends ChunkGenerator
{
    public function render($options = array()): string
    {
        return $this->_render('chunk/boolean.twig', $options);
    }
}
