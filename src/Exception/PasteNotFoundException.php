<?php

namespace Icawebdesign\Hibp\Exception;

use JetBrains\PhpStorm\Pure;
use RuntimeException;
use function get_class;

class PasteNotFoundException extends RuntimeException
{
    /**
     * PasteNotFoundException constructor
     *
     * @param string|null $message
     * @param int $code
     */
    public function __construct(string $message = null, int $code = 0)
    {
        if (!$message) {
            throw new $this(sprintf('Unknown %s', get_class($this)));
        }
        
        parent::__construct($message, $code);
    }

    public function __toString(): string
    {
        return sprintf(
            "%s %s in %s(%s)\n%s",
            get_class($this),
            $this->message,
            $this->file,
            $this->line,
            $this->getTraceAsString()
        );
    }
}
