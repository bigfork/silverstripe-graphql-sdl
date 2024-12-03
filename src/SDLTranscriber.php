<?php

namespace Bigfork\SilverstripeGraphQLSDL;

use Exception;
use GraphQL\Type\Schema;
use GraphQL\Utils\SchemaPrinter;
use SilverStripe\Assets\Storage\GeneratedAssetHandler;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\Core\Path;
use Symfony\Component\Filesystem\Filesystem;

class SDLTranscriber
{
    use Injectable;

    const CACHE_FILENAME = 'sdl.graphql';

    /**
     * @var Schema
     */
    private $schema;

    /**
     * @var string
     */
    private $name;

    /**
     * @var GeneratedAssetHandler
     */
    protected $assetHandler;

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var string
     */
    private $rootDir;

    public function __construct(Schema $schema, string $name)
    {
        $this->fs = new Filesystem();
        $this->schema = $schema;
        $this->name = $name;
        $this->rootDir = Path::join(PUBLIC_PATH, '_graphql');
    }

    /**
     * @throws Exception
     */
    public function writeSDLToFilesystem(): void
    {
        try {
            $schemaConfig = $this->schema->getConfig();
            $coreTypeLoader = $schemaConfig->getTypeLoader();

            // Silverstripe type loader throws exceptions when it encounters unknown types
            // However, sometimes native types like "Mutation" might be unknown if there
            // are no mutations registered, so we need to catch those exceptions if the
            // schema printer tries to load those types
            $schemaConfig->setTypeLoader(function (string $typeName) use ($coreTypeLoader) {
                try {
                    return call_user_func($coreTypeLoader, $typeName);
                } catch (Exception $e) {
                    return null;
                }
            });

            $schema = SchemaPrinter::doPrint($this->schema);
        } catch (Exception $e) {
            throw new Exception(sprintf(
                'There was an error creating the GraphQL SDL file: %s',
                $e->getMessage()
            ));
        }

        $this->writeSDL($schema);
    }

    public function writeSDL(string $content): void
    {
        $this->fs->dumpFile($this->generateCacheFilename(), $content);
    }

    private function generateCacheFilename(): string
    {
        return Path::join(
            $this->rootDir,
            $this->name . '.' . self::CACHE_FILENAME
        );
    }
}
