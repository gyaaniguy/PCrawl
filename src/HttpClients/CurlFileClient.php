<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use Gyaaniguy\PCrawl\Response\PResponse;
use InvalidArgumentException;

class CurlFileClient extends CurlClient
{
    public array $defaultOptions = [
        'user_agent' => 'File bot',
    ];

    /**
     * Download a file.
     * @param string $url Url of the file to download
     * @param array $options 'filepath' key should have the value of the path to save the file
     * @return PResponse
     * @throws InvalidArgumentException
     */
    public function get(string $url, array $options = []): PResponse
    {
        if (empty($options) || empty($options['filepath'])) {
            throw new InvalidArgumentException ('No filepath provided');
        }
        $fp = fopen($options['filepath'], 'w+');
        if (!$fp) {
            throw new InvalidArgumentException ('filepath is not writable');
        }
        $this->setCustomClientOptions([
            CURLOPT_FILE => $fp,
        ]);
        return parent::get($url);
    }
}