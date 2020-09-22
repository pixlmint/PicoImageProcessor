<?php

/**
 * Class ImageProcessor
 */
class PicoImageProcessor extends AbstractPicoPlugin
{
    private array $sizes;
    private array $config;
    private string $basePicturesPath;
    private string $absoluteAssetsPath;
    private string $relativePicturesPath;
    private bool $downloadImages;
    private bool $allowUnsafeSources;

    public function __construct(Pico $pico)
    {
        parent::__construct($pico);
        $this->sizes = ['thumb' => 200, '400' => 400, '500' => 500, 1000 => 1000];
        $this->config = [];
        $this->basePicturesPath = '/var/www/html/assets/pictures';
        $this->absoluteAssetsPath = '/var/www/html';
        $this->relativePicturesPath = '/var/www/html/assets';
        $this->downloadImages = true;
        $this->allowUnsafeSources = false;
    }

    public function onConfigLoaded(array $config)
    {
        if (key_exists('sizes', $config)) {
            $this->sizes = $config['sizes'];
        }
        if (key_exists('pictures_path', $config)) {
            $this->basePicturesPath = $config['pictures_path'];
        }
        if (key_exists('absolute_assets_path', $config)) {
            $this->absoluteAssetsPath = $config['absolute_assets_path'];
        }
        if (key_exists('download_images', $config)) {
            $this->downloadImages = $config['download_images'];
        }
        if (key_exists('allow_unsafe_sources', $config)) {
            $this->allowUnsafeSources = $config['allow_unsafe_sources'];
        }
        $this->relativePicturesPath = $config['assets_url'] . 'pictures';

        $this->config = $config;

        foreach ($this->sizes as $key => $size) {
            if (!is_dir($this->basePicturesPath . '/' . $key)) {
                mkdir($this->basePicturesPath . '/' . $key);
            }
        }
    }

    /**
     * @param Twig_Environment $twig
     * @param array            $twigVariables
     * @param string           $templateName
     * @throws Exception
     */
    public function onPageRendering(Twig_Environment &$twig, array &$twigVariables, &$templateName)
    {
        if (key_exists('thumbnail', $twigVariables['meta'])) {
            $newThumb = $this->createThumbnail($twigVariables['meta']['thumbnail']);
            $twigVariables['meta']['thumbnail'] = $newThumb;
        }
        foreach ($twigVariables['pages'] as $page) {
            if (key_exists('thumbnail', $page['meta']) && $page['url'] !== $twigVariables['current_page']['url']) {
                $page['meta']['thumbnail'] = $this->createThumbnail($page['meta']['thumbnail']);
            }
        }

        $doc = new DOMDocument();
        $doc->loadHTML($twigVariables['content']);

        foreach ($doc->getElementsByTagName('img') as $img) {
            $newPics = $this->createThumbnail($img->attributes->getNamedItem('src')->nodeValue);
            $img->setAttribute('src', $newPics['1000']);
            $doc->importNode($img, true);
        }

        $twigVariables['content'] = $doc->saveHTML();
    }

    private function downloadImage(string $imageUrl)
    {
        $original = file_get_contents($imageUrl);
        $filename = explode('/', $imageUrl);
        $filename = array_pop($filename);
        $originalPath = $this->basePicturesPath . '/' . $filename;
        file_put_contents($originalPath, $original);

        return $this->relativePicturesPath . '/' . $filename;
    }

    private function createThumbnail(string $imageUrl): array
    {
        if (substr($imageUrl, 0, 4) === "http") {
            if (!$this->allowUnsafeSources && substr($imageUrl, 0, 5) === "http:") {
                throw new Exception('Images from unsecure sources are not allowed');
            }
            if (!$this->downloadImages) {
                $ret = [];
                foreach ($this->sizes as $key => $size) {
                    $ret[$key] = $imageUrl;
                }

                return $ret;
            }

            $imageUrl = $this->downloadImage($imageUrl);
        }
        $path = explode('/', $imageUrl);
        $filename = array_pop($path);
        $relativePath = implode('/', $path);
        $newPath = $this->absoluteAssetsPath . $relativePath;

        $images = [];

        foreach ($this->sizes as $key => $size) {
            $newStrPath = $this->basePicturesPath . "/${key}/${filename}";
            if (!is_file($newStrPath)) {
                try {
                    $img = imagecreatefromstring(file_get_contents("${newPath}/${filename}"));
                    $thumbnail = imagescale($img, $size);
                    imagejpeg($thumbnail, $newStrPath);
                } catch (\Exception $e) {
                    echo($e);
                }
            }
            $images[$key] = "${relativePath}/${key}/${filename}";
        }

        return $images;
    }
}