<?php

namespace App\Service;

use App\Entity\Storage\File;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class DownloadService
 * @package App\Service
 */
class DownloadService
{
    /**
     * @var File
     */
    private $file;

    /**
     * @var string
     */
    private $host;

    /**
     * @var resource
     */
    private $context;

    /**
     * DownloadService constructor.
     */
    public function __construct()
    {
        $this->host = getenv('UPLOADER_URL');

        if ($this->host === false) {
            throw new InvalidConfigurationException('Environment directive `UPLOADER_URL` does not set.');
        }

        $this->host = trim($this->host, '/');

        $this->context = stream_context_create([
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ]
        ]);
    }

    /**
     * @param File $entity
     * @param string $rename
     *
     * @return Response
     */
    public function download(File $entity, $rename = ''): Response
    {
        $this->file = $entity;

        if (!$this->checkAccess()) {
            throw new HttpException(403, "You don't have privileges to download this file.");
        }

        if (!$this->validateDownload()) {
            throw new HttpException(400, 'File checksum is invalid.');
        }

        if ($stream = fopen($this->host . "/" . $this->file->getUuid() . "/download", 'rb', null, $this->context)) {
            $name = $rename ? $rename : $this->file->getName();
            $name = preg_replace('/[\s,]/', '_', $name);
            $size = $this->file->getSize();
            $bytes = 1048576;
            $content = '';

            $response = new Response();
            $response->headers->add([
                "Content-Type" => $this->file->getType()/*"application/octet-stream"*/,
                "Content-Length" => $size,
                "Content-Disposition" => "attachment; filename=\"$name\"",
                "Pragma" => "public",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0"
            ]);

            while (!feof($stream)) {
                $content .= stream_get_contents($stream, $bytes, -1);
            }

            fclose($stream);

            $response->setContent($content);
            $this->updateStats();

            return $response;
        }

        throw new HttpException(404, "Could not get requested file.");
    }

    /**
     * @return bool
     */
    private function validateDownload(): bool
    {
        $result = false;

        if ($stream = fopen($this->host . "/" . $this->file->getUuid() . "/validate?hash=" . $this->file->getHash(), 'r', null, $this->context)) {
            $data = stream_get_contents($stream);
            try {
                $data = json_decode($data);
                $result = is_bool($data->valid) && $data->valid;
            }
            catch (\Exception $exception) {
                $result = false;
            }

            fclose($stream);
        }

        return $result;
    }

    /**
     * Check privileges before downloading file.
     * @return bool
     * @todo check user privileges before downloading a file
     */
    private function checkAccess(): bool
    {
        return true;
    }

    /**
     * Updates download statistics.
     * @todo update file stats after downloading
     */
    private function updateStats(): void
    {
        return;
    }
}