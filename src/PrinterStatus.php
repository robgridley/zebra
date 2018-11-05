<?php

namespace Zebra;


/**
 * Class Status
 *
 * This class is a translation of the results of an `~HS` ZPL command return so that its values can easily be used
 * without needing the specific knowledge of the return codes.
 *
 * @see https://support.zebra.com/cpws/docs/zpl/zpl_manual.pdf (pages 227-229, Host Status Return)
 *
 * @package Zebra
 */
class PrinterStatus
{
    const POS_COMM_SETTINGS = 0;
    const POS_PAPER_OUT = 1;
    const POS_PAUSED = 2;
    const POS_LABEL_LENGTH = 3;
    const POS_FORMATS_IN_BUFFER = 4;
    const POS_BUFFER_FULL = 5;
    const POS_COMM_DIAG_MODE = 6;
    const POS_PARTIAL_FORMAT = 7;
    const POS_CORRUPT_RAM = 9;
    const POS_OVER_TEMP = 10;
    const POS_UNDER_TEMP = 11;
    const POS_FUNCTION_SETTINGS = 12;
    const POS_HEAD_UP = 14;
    const POS_RIBBON_OUT = 15;
    const POS_THERMAL_TRANSFER_MODE = 16;
    const POS_PRINT_MODE = 17;
    const POS_PRINT_WIDTH_MODE = 18;
    const POS_LABEL_WAITING = 19;
    const POS_LABELS_REMAINING = 20;
    const POS_FORMAT_WHILE_PRINTING = 21;
    const POS_IMAGES_IN_MEMORY = 22;
    const POS_PASSWORD = 23;
    const POS_STATIC_RAM_INSTALLED = 24;

    const PARITY_EVEN = 'EVEN';
    const PARITY_ODD = 'ODD';

    const BAUD_CODES = [
        '0000' => 110,
        '0001' => 300,
        '0010' => 600,
        '0011' => 1200,
        '0100' => 2400,
        '0101' => 4800,
        '0110' => 9600,
        '0111' => 19200,
        '1000' => 28800,
        '1001' => 38400,
        '1010' => 57600,
        '1011' => 14400,
    ];

    const PRINT_MODES = [
        '0' => "Rewind",
        '1' => "Peel-Off",
        '2' => "Tear-Off",
        '3' => "Cutter",
        '4' => "Applicator",
        '5' => "Delayed cut",
        '6' => "Linerless Peel",
        '7' => "Linerless Rewind",
        '8' => "Partial Cutter",
        '9' => "RFID",
        'A' => "Linerless Cut",
        'B' => "Linerless Delayed Cut",
        'K' => "Kiosk",
        'S' => "Stream",
    ];
    
    /**
     * This is the parsed ordered list of values from the `~HS` command. Use the `POS_*` constants to determine the
     * correct index of the value needed.
     *
     * e.g.
     * ```
     * Array
     * (
     *   [0] => 030
     *   [1] => 0
     *   [2] => 0
     *   [3] => 0482
     *   [4] => 000
     *   [5] => 0
     *   //...
     *```
     *
     * @var array
     */
    private $data;

    public function __construct($arrayData)
    {
        $this->data = $arrayData;
    }

    /**
     * Creates a new object from the raw output of the `~HS` command.
     *
     * @param string $response
     * @return PrinterStatus
     */
    public static function createFromRawResponse(string $response): self
    {
        // Strip out STX ETX characters
        $response = preg_replace('/[\x02\x03]/','',$response);

        // Split the lines
        $lines = explode("\r\n", $response);

        // Split the values of each line and merge them together
        $data = [];
        foreach ($lines as $line) {
            $data = array_merge($data, explode(',', $line));
        }

        return new static($data);
    }

    /**
     * Returns the current baud rate of the printer.
     *
     * @return int
     */
    public function getBaudRate() : int
    {
        $code =
            $this->getCommBit(8) .
            $this->getCommBit(2) .
            $this->getCommBit(1) .
            $this->getCommBit(0);

        return self::BAUD_CODES[$code];
    }

    /**
     * Returns the type of handshake the printer is configured for. ('Xon/Xoff' or 'DTR')
     *
     * @return string
     */
    public function getHandshakeType() : string
    {
        switch ($this->getCommBit(7)) {
            case 0:
                return 'Xon/Xoff';
            case 1:
                return 'DTR';
        }
    }

    /**
     * Returns PrinterStatus::PARITY_EVEN or PrinterStatus::PARITY_ODD. If serial communication is disabled return null.
     *
     * @return null|string
     */
    public function getParity() : ?string
    {
        if (!$this->isSerialEnabled()) {
            return null;
        }

        switch ($this->getCommBit(6)) {
            case 0:
                return self::PARITY_ODD;
            case 1:
                return self::PARITY_EVEN;
        }
    }

    /**
     * Return true if serial communication is enabled
     *
     * @return bool
     */
    public function isSerialEnabled() : bool
    {
        return !!$this->getCommBit(5);
    }

    /**
     * @return int
     */
    public function getStopBits() : int
    {
        switch ($this->getCommBit(4)) {
            case 0:
                return 1;
            case 1:
                return 2;
        }
    }

    /**
     * @return int
     */
    public function getDataBits() : int
    {
        switch ($this->getCommBit(3)) {
            case 0:
                return 7;
            case 1:
                return 8;
        }
    }

    /**
     * @return bool
     */
    public function isPaperOut() : bool
    {
        return !!$this->data[self::POS_PAPER_OUT];
    }

    /**
     * @return bool
     */
    public function isPaused() : bool
    {
        return !!$this->data[self::POS_PAUSED];
    }

    /**
     * Returns the label length (value in number of dots)
     *
     * @return int
     */
    public function getLabelLength() : int
    {
        return (int) $this->data[self::POS_LABEL_LENGTH];
    }

    /**
     * Returns the number of formats in receive buffer
     *
     * @return int
     */
    public function getFormatCountInBuffer() : int
    {
        return (int) $this->data[self::POS_FORMATS_IN_BUFFER];
    }

    /**
     * @return bool
     */
    public function isBufferFull() : bool
    {
        return !!$this->data[self::POS_BUFFER_FULL];
    }

    /**
     * @return bool
     */
    public function isDiagnosticModeActive() : bool
    {
        return !!$this->data[self::POS_COMM_DIAG_MODE] || !!$this->getMediaBit(5);
    }

    /**
     * @return bool
     */
    public function isPartialFormatInProgress() : bool
    {
        return !!$this->data[self::POS_PARTIAL_FORMAT];
    }

    /**
     * @return bool
     */
    public function isRamCorrupt() : bool
    {
        return !!$this->data[self::POS_CORRUPT_RAM];
    }

    /**
     * @return bool
     */
    public function isUnderTemperature() : bool
    {
        return !!$this->data[self::POS_UNDER_TEMP];
    }

    /**
     * @return bool
     */
    public function isOverTemperature() : bool
    {
        return !!$this->data[self::POS_OVER_TEMP];
    }

    /**
     * @return bool
     */
    public function isMediaDieCut() : bool
    {
        return $this->getMediaBit(7) === 0;
    }

    /**
     * @return bool
     */
    public function isMediaContinuous() : bool
    {
        return $this->getMediaBit(7) === 1;
    }

    /**
     * Returns true if the print head (lid) is open
     *
     * @return bool
     */
    public function isHeadUp() : bool
    {
        return !!$this->data[self::POS_HEAD_UP];
    }

    /**
     * @return bool
     */
    public function isRibbonOut() : bool
    {
        return !!$this->data[self::POS_RIBBON_OUT];
    }

    /**
     * @return bool
     */
    public function isThermalTransferMode() : bool
    {
        return !!$this->data[self::POS_THERMAL_TRANSFER_MODE] || $this->getMediaBit(0) === 1;
    }

    /**
     * @return bool
     */
    public function isDirectThermalMode() : bool
    {
        return !$this->data[self::POS_THERMAL_TRANSFER_MODE] && $this->getMediaBit(0) === 0;
    }

    /**
     * Returns the current print mode. See `PrinterStatus::POS_PRINT_MODE`
     *
     * @return string
     */
    public function getPrintMode() : string
    {
        return self::PRINT_MODES[$this->data[self::POS_PRINT_MODE]];
    }

    /**
     * ZPL Documentation doesn't really give much of a clue to what the return value means.
     *
     * @return int
     */
    public function getPrintWidthMode() : int
    {
        return (int) $this->data[self::POS_PRINT_WIDTH_MODE];
    }

    /**
     * Returns true if label waiting in Peel-off Mode
     *
     * @return bool
     */
    public function isLabelWaiting() : bool
    {
        return !!$this->data[self::POS_LABEL_WAITING];
    }

    /**
     * @return int
     */
    public function getLabelsRemainingCount() : int
    {
        return (int) $this->data[self::POS_LABELS_REMAINING];
    }

    /**
     * Documentation states that this will always return true
     *
     * @return bool
     */
    public function isFormatWhilePrinting() : bool
    {
        return !!$this->data[self::POS_FORMAT_WHILE_PRINTING];
    }

    /**
     * @return int
     */
    public function getImagesInMemoryCount() : int
    {
        return (int) $this->data[self::POS_IMAGES_IN_MEMORY];
    }

    /**
     * Dear God, why?!
     *
     * @return string
     */
    public function getPassword() : string
    {
        return $this->data[self::POS_PASSWORD];
    }

    /**
     * @return bool
     */
    public function hasStaticRam() : bool
    {
        return !!$this->data[self::POS_STATIC_RAM_INSTALLED];
    }

    /**
     * Returns the the value of a specific bit in the communications settings value
     *
     * @param int $bit Zero based binary position
     * @return int
     */
    private function getCommBit(int $bit) : int
    {
        $asInt = intval($this->data[self::POS_COMM_SETTINGS], 10);

        $mask = 1 << $bit;

        return $asInt & $mask ? 1 : 0;
    }

    /**
     * Returns the the value of a specific bit in the function settings value
     *
     * @param int $bit Zero based binary position
     * @return int
     */
    private function getMediaBit(int $bit) : int
    {
        $asInt = intval($this->data[self::POS_FUNCTION_SETTINGS], 10);

        $mask = 1 << $bit;

        return $asInt & $mask ? 1 : 0;
    }
}
