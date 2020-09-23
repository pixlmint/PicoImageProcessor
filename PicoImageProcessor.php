<?php

/**
 * Class ImageProcessor
 */
class PicoImageProcessor extends AbstractPicoPlugin
{
    private array $sizes= ['thumb' => 200, '400' => 400, '500' => 500, 1000 => 1000, 'max' => 1080];
    private array $config;


    private string $absoluteWebDirectory = '/var/www/html';
    private string $relativePicturesPath = '/assets/pictures';
    private string $relativeScaledImagePath = '/assets/thumbs';
    private string $relativeDownloadsPath = '/assets/downloads';
    private bool $downloadImages = true;
    private bool $allowUnsafeSources = false;
    protected $enabled = true;

    public function __construct(Pico $pico)
    {
        parent::__construct($pico);
        $this->config = [];
    }

    public function onConfigLoaded(array $config)
    {
        $this->config = $config;
        if (key_exists('PicoImageProcessor', $config)) {
            foreach (get_object_vars($this) as $key => $val) {
                if (key_exists($key, $config['PicoImageProcessor']) && $key !== 'config') {
                    $this->$key = $config['PicoImageProcessor'][$key];
                }
            }
        }

        foreach ($this->sizes as $key => $size) {
            $dir = $this->absoluteWebDirectory . $this->relativeScaledImagePath . '/' . $key;
            if (!is_dir($dir)) {
                if (!mkdir($dir, 0777, true)) {
                    echo("Failed creating ${dir}<br>");
                }
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
        if (!is_dir($this->absoluteWebDirectory . $this->relativeDownloadsPath)) {
            mkdir($this->absoluteWebDirectory . $this->relativeDownloadsPath, 0777, true);
        }
        $original = file_get_contents($imageUrl);
        $filename = explode('/', $imageUrl);
        $filename = array_pop($filename);
        $originalPath = $this->absoluteWebDirectory . $this->relativeDownloadsPath . '/' . $filename;
        file_put_contents($originalPath, $original);

        return $this->relativePicturesPath . '/' . $filename;
    }

    /**
     * @param string $imageUrl
     * @return array
     * @throws Exception
     */
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
        $picturePath = $this->absoluteWebDirectory .  implode('/', $path);

        $images = [];

        foreach ($this->sizes as $key => $size) {
            $newStrPath = $this->absoluteWebDirectory . $this->relativeScaledImagePath . "/${key}/${filename}";
            if (!is_file($newStrPath)) {
                try {
                    $img = imagecreatefromstring(file_get_contents("${picturePath}/${filename}"));
                    $thumbnail = imagescale($img, $size);
                    imagejpeg($thumbnail, $newStrPath);
                } catch (\Exception $e) {
                    echo ($e);
                }
            }
            $images[$key] = $this->relativeScaledImagePath . "/${key}/${filename}";
        }

        return $images;
    }
}
