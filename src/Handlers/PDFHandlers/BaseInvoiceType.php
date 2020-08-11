<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Handlers\PDFHandlers;

use App\Entity\Invoice;
use Symfony\Component\Translation\TranslatorInterface;

use Doctrine\ORM\EntityManagerInterface;

use App\FPDF\PdfInvoice;

use App\Storage\InterfaceStorage;

/**
 * Strategy pattern
 *
 * The strategy pattern is a behavioral software design pattern that enables selecting an algorithm at runtime.
 * Instead of implementing a single algorithm directly,
 * code receives run-time instructions as to which in a family of algorithms to use.
 *
 * @see \App\Handlers\PdfGenerator
 */
abstract class BaseInvoiceType
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var InterfaceStorage
     */
    protected $storage;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var PdfInvoice
     */
    protected $pdf;

    /**
     * @var string
     */
    protected $media;
    protected $logoPath;

    /**
     * Constructor
     *
     * @param EntityManagerInterface    $em
     * @param TranslatorInterface       $translator
     * @param InterfaceStorage          $storage
     * @param array                     $fpdfConfigs
     */
    public function __construct(EntityManagerInterface $em, TranslatorInterface $translator, InterfaceStorage $storage, array $fpdfConfigs)
    {
        $this->translator = $translator;
        $this->storage = $storage;
        $this->em = $em;

        // set PDF configuration
        $this->logoPath = $fpdfConfigs['logoPath'];
        $this->media = $fpdfConfigs['media'];

        // init fpdf
        $this->pdf = new PdfInvoice();
    }

    /**
     * This method create monthly invoice and save it in the system file.
     *
     * @param Invoice $invoice
     *
     * @return void
     */
    public abstract function createInvoicePdf(Invoice $invoice): void;

    /**
     * Set price filter
     *
     * @param float|null    $number
     * @param int           $decimals
     * @param string        $decPoint
     * @param string        $thousandsSep
     *
     * @return string
     */
    protected static function priceFormatter(?float $number, int $decimals = 2, string $decPoint = ',', string $thousandsSep = ''): string
    {
        return number_format($number, $decimals, $decPoint, $thousandsSep);
    }
}
