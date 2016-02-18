<?php

namespace Zebra;

class Client
{
    /**
     * The endpoint.
     *
     * @var resource
     */
    protected $socket;

    /**
     * Create an instance.
     *
     * @param string $host
     * @param int $port
     */
    public function __construct($host, $port = 9100)
    {
        $this->connect($host, $port);
    }

    /**
     * Destroy an instance.
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * Create an instance statically.
     *
     * @param string $host
     * @param int $port
     * @return self
     */
    public static function printer($host, $port = 9100)
    {
        return new static($host, $port);
    }

    /**
     * Connect to printer.
     *
     * @param string $host
     * @param int $port
     * @return bool
     * @throws CommunicationException if the connection fails.
     */
    protected function connect($host, $port)
    {
        $this->socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if (!$this->socket || !@socket_connect($this->socket, $host, $port)) {
            $error = $this->getLastError();
            throw new CommunicationException($error['message'], $error['code']);
        }
    }

    /**
     * Close connection to printer.
     */
    protected function disconnect()
    {
        @socket_close($this->socket);
    }

    /**
     * Send ZPL data to printer.
     *
     * @param string $zpl
     * @throws CommunicationException if writing to the socket fails.
     */
    public function send($zpl)
    {
        if (!@socket_write($this->socket, $zpl)) {
            $error = $this->getLastError();
            throw new CommunicationException($error['message'], $error['code']);
        }
    }

    /**
     * Get the last socket error.
     *
     * @return array
     */
    protected function getLastError()
    {
        $code = socket_last_error($this->socket);
        $message = socket_strerror($code);

        return compact('code', 'message');
    }
}
