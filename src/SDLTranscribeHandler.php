<?php

namespace Bigfork\SilverstripeGraphQLSDL;

use Exception;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\EventDispatcher\Event\EventContextInterface;
use SilverStripe\EventDispatcher\Event\EventHandlerInterface;
use SilverStripe\GraphQL\Schema\SchemaBuilder;

class SDLTranscribeHandler implements EventHandlerInterface
{
    use Configurable;

    /**
     * @var array
     */
    private static $schemas = [];

    /**
     * @param EventContextInterface $context
     * @throws Exception
     */
    public function fire(EventContextInterface $context): void
    {
        $schemaKey = $context->getAction();
        $allowedSchemas = $this->config()->get('schemas');
        if (!empty($allowedSchemas) && !in_array($schemaKey, $allowedSchemas)) {
            return;
        }

        $schema = SchemaBuilder::singleton()->getSchema($schemaKey);
        if (!$schema) {
            return;
        }

        $inst = SDLTranscriber::create($schema, $schemaKey);
        $inst->writeSDLToFilesystem();
    }
}
