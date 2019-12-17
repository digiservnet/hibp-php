<?php

namespace Icawebdesign\Hibp\Exception;

use RuntimeException;
use function get_class;

class BreachNotFoundException extends RuntimeException
{
    /**
     * BreachNotFoundException constructor.
     *
     * @param string|null $message
     * @param int $code
     */
    public function __construct(string $message = null, int $code = 0)
    {
        if (!$message) {
            throw new $this('Unknown ' . get_class($this));
        }

        parent::__construct($message, $code);
    }

    public function __toString()
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
