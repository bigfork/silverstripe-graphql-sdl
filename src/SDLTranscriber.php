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
            $schema = SchemaPrinter::doPrint($this->schema);
        } catch (Exception $e) {
            throw new Exception(sprintf(
                'There was an error creating the GraphQL SDL: %s',
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
