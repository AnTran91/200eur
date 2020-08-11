<?php

namespace App\FPDF;

use App\Entity\Promo;
use App\Entity\Transaction;
use Symfony\Component\Translation\TranslatorInterface;

use App\Entity\Invoice;
use App\Entity\User;
use App\Entity\Order;

define('EURO', chr(128));

class PdfInvoice extends \FPDF
{
    /**
     * Constant x and y used to create the pdf
     */
    const TOTAL_PRICE_TABLE_X_HEIGHT = 125;
    const DETAIL_TABLE_MAX_Y = 215;
    const ORDERS_TABLE_MAX_Y = 220;
    
    /**
     * @var TranslatorInterface
     */
    private $translator;
    
    /**
     * @var string
     */
    private $currentY;
    private $currentX;
    private $widths;
    private $aligns;
    private $invoiceType;

    private $beforeWallet = null;
    private $afterWallet = null;
    
    /**
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    
    // Page header
    public function setHeader(string $logoPath)
    {
        // Logo
        $this->Image($logoPath, 75, 4, 65, -1400);
        $this->Ln(55);
    }
    
    public function setInvoiceType(string $type) {
        $this->invoiceType = $type;
    }
    
    public function setInvoiceTitle(Invoice $invoice, string $date)
    {
        $invoiceNumber = $invoice->getInvoiceNumber();
        
        $this->SetXY(10, $this->GetY());
        
        $this->SetTextColor(233, 50, 177);
        $this->SetFont('Times', 'B', 18);
        $this->SetDrawColor(233, 50, 177);
        $this->SetFillColor(255);
        $this->SetLineWidth(.4);
        if ($this->invoiceType == 'invoice.types.additional_invoice_wallet' || $this->invoiceType == 'invoice.types.wallet_invoice') {
            $this->Cell(190, 10, utf8_decode($this->translator->trans('fpdf_invoice.wallet_invoice_title', ['%date%' => $date])), 1, 0, 'C', true);
        } else {
            $this->Cell(190, 10, utf8_decode($this->translator->trans('fpdf_invoice.invoice', ['%num%' => $invoiceNumber, '%date%' => $date])), 1, 0, 'C', true);
        }
        
        $this->Ln(20);
    }

    public function setRechargeInvoiceTitle(Invoice $invoice, string $date)
    {
        $invoiceNumber = $invoice->getInvoiceNumber();
        
        $this->SetXY(10, $this->GetY());
        
        $this->SetTextColor(233, 50, 177);
        $this->SetFont('Times', 'B', 18);
        $this->SetDrawColor(233, 50, 177);
        $this->SetFillColor(255);
        $this->SetLineWidth(.4);
        $this->Cell(190, 10, utf8_decode($this->translator->trans('fpdf_invoice.recharge_invoice', ['%num%' => $invoiceNumber, '%date%' => $date])), 1, 0, 'C', true);
        
        $this->Ln(20);
    }
    
    public function setClient(User $user)
    {
        $this->setXY(9, $this->GetY());
        $this->SetFont('Times', 'B', 12);
        $this->Cell(30, 0, utf8_decode($this->translator->trans('fpdf_invoice.client')));
        
        $this->setXY(115, $this->GetY());
        $this->SetTextColor(62, 62, 62);
        $this->Cell(30, 0, utf8_decode($user->getFullName()));
        $this->Ln(5);
        
        $this->setXY(115, $this->GetY());
        $this->Cell(0, 2, utf8_decode($user->getBillingAddress()->getCompany()));
        $this->Ln(5);
        
        $this->SetFont('Times', '', 12);
        $this->setXY(115, $this->GetY());
        $this->Cell(0, 3, utf8_decode($user->getBillingAddress()->getAddress()));
        $this->Ln(5);
        
        $this->setXY(115, $this->GetY());
        $this->Cell(0, 4, utf8_decode($user->getBillingAddress()->getFullAddress()));
        $this->Ln(5);
        
        $this->setXY(115, $this->GetY());
        $this->Cell(0, 4, utf8_decode($this->translator->trans('fpdf_invoice.user_tva_info', ['%num' => $user->getBillingAddress()->getTVA()])));
        $this->Ln(10);
    }
    
    /**
     * @param Order[] $orders
     */
    public function setOrders($orders)
    {
        $this->setXY($this->GetX(), $this->GetY() + 6);
        $this->SetDrawColor(62, 62, 62);
        $this->SetLineWidth(.2);
        // Column widths
        $w = array(45, 35, 80, 30);
        
        $this->SetFillColor(233, 50, 177);
        $this->Cell(array_sum($w), 10, utf8_decode($this->translator->trans('fpdf_invoice.work')), 'TLR', 0, 'C');
        $this->Ln();
        
        $this->SetFillColor(255);
        
        $header = array(
            $this->translator->trans('fpdf_invoice.order_num'),
            $this->translator->trans('fpdf_invoice.create_date'),
            $this->translator->trans('fpdf_invoice.promo_code'),
            $this->translator->trans('fpdf_invoice.total_wt')
        );
        // Header
        for ($i = 0; $i < count($header); $i++) {
            $this->CellFitScale($w[$i], 7, utf8_decode($header[$i]), 1, 0, 'C');
        }
        $this->Ln();
        // Data
        foreach ($orders as $order) {
            if ($order->getOrderStatus() == Order::DECLINED_BY_PRODUCTION) {
                continue;
            }

            $this->CellFitScale($w[0], 6, sprintf('%04d', $order->getOrderNumber()), 'LR', 0, 'C');
            $this->CellFitScale($w[1], 6, $order->getCreationDate()->format('d/m/Y'), 'LR', 0, 'C');
            $this->CellFitScale($w[2], 6, !is_null($order->getPromotion()) ? $order->getPromotion()->getPromoCode() : '', 'LR', 0, 'C');
            $this->CellFitScale($w[3], 6, $this->concatEuro($order->getTotalAmount()), 'LR', 0, 'C');
            $this->Ln();
        }
        // spacing
        foreach ($w as $value) {
            $this->Cell($value, (self::ORDERS_TABLE_MAX_Y - $this->GetY()) > 10 ? (self::ORDERS_TABLE_MAX_Y - $this->GetY()) : 10, ' ', 'LR', 0);
        }
        
        $this->Ln();
        
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
        $this->Ln(10);
    }
    
    public function setReductionTable($data)
    {
        $this->setXY($this->GetX(), $this->GetY() + 6);
        $this->SetDrawColor(62, 62, 62);
        $this->SetLineWidth(.2);
        // Column widths
        $w = array(35, 60, 35, 30, 30);
        
        $this->SetFillColor(233, 50, 177);
        $this->Cell(array_sum($w), 10, utf8_decode($this->translator->trans('fpdf_invoice.work')), 'TLR', 0, 'C');
        $this->Ln();
        
        $this->SetFillColor(255);
        
        $header = array(
            $this->translator->trans('fpdf_invoice.order_num'),
            $this->translator->trans('fpdf_invoice.agent_name'),
            $this->translator->trans('fpdf_invoice.create_date'),
            $this->translator->trans('fpdf_invoice.picture_number'),
            $this->translator->trans('fpdf_invoice.total_wt')
        );
        // Header
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 7, utf8_decode($header[$i]), 1, 0, 'C');
        }
        $this->Ln();
        // Data
        foreach ($data as $row) {
            $this->CellFitScale($w[0], 6, $row['order_number'], 'LR', 0, 'C');
            $this->CellFitScale($w[1], 6, utf8_decode($row['order_client']), 'LR', 0, 'C');
            $this->CellFitScale($w[2], 6, $row['order_creation_date'], 'LR', 0, 'C');
            $this->CellFitScale($w[3], 6, $row['order_reduction_picture'], 'LR', 0, 'C');
            $this->CellFitScale($w[4], 6, $this->concatEuro($row['order_price']), 'LR', 0, 'C');
            $this->Ln();
        }
        // spacing
        foreach ($w as $value) {
            $this->Cell($value, (self::DETAIL_TABLE_MAX_Y - $this->GetY()) > 10 ? (self::DETAIL_TABLE_MAX_Y - $this->GetY()-10) : 10, ' ', 'LR', 0);
        }
        $this->Ln();
        
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
        $this->Ln(10);
    }
    
    public function setOrder(Order $order)
    {
        $this->SetTextColor(233, 50, 177);
        
        $this->setXY(9, $this->GetY());
        $this->SetFont('Times', 'B', 12);
        $this->Cell(30, 0, utf8_decode($this->translator->trans('fpdf_invoice.work')));
        
        $this->setXY(115, $this->GetY());
        $this->SetFont('Times', '', 12);
        $this->SetTextColor(62, 62, 62);
        $this->Cell(30, 0, utf8_decode($this->translator->trans('fpdf_invoice.work_desc')));
        $this->Ln(5);
        
        $this->setXY(115, $this->GetY());
        $this->SetFont('Times', 'B', 12);
        if ($this->invoiceType == 'invoice.types.additional_invoice') {
            $this->Cell(0, 2, utf8_decode($this->translator->trans('fpdf_invoice.order_additional')));
            $this->Ln(5);
            $this->setXY(115, $this->GetY());
        }
        else {
            $this->Cell(0, 2, utf8_decode($this->translator->trans('fpdf_invoice.order')));
            $this->setXY(142, $this->GetY());
        }
        $this->SetFont('Times', '', 12);
        $this->Cell(0, 2, utf8_decode($this->translator->trans('fpdf_invoice.order_info', ['%num%' => sprintf('%04d', $order->getOrderNumber()), '%date%' => $order->getCreationDate()->format('d-m-Y')])));
        $this->Ln(5);
        
        $this->setXY(115, $this->GetY());
        $this->Cell(0, 3, utf8_decode('www.emmobilier.fr'));
        $this->Ln(5);
        
        $this->setXY(115, $this->GetY());
        $this->Cell(0, 4, utf8_decode(''));
        $this->Ln(5);
    }
    
    public function setTable(?array $retouchs, ?array $settings)
    {
        if ($this->GetY() + 100 > $this->PageBreakTrigger) {
            $this->AddPage($this->CurOrientation);
            $this->currentX = $this->GetX();
            $this->currentY = 20;
        } else {
            // cuurent
            $this->currentX = $this->GetX();
            $this->currentY = $this->GetY();
        }
        
        $header = array($this->translator->trans('fpdf_invoice.Designation'),
            $this->translator->trans('fpdf_invoice.amount'),
            $this->translator->trans('fpdf_invoice.unit_price'),
            $this->translator->trans('fpdf_invoice.vat'),
            $this->translator->trans('fpdf_invoice.total_wt'));
        
        $this->SetDrawColor(62, 62, 62);
        $this->SetFillColor(255);
        $this->SetLineWidth(.2);
        // Column widths
        $w = array(110, 15, 20, 20, 29);
        // Header
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 7, utf8_decode($header[$i]), 1, 0, 'C');
        }
        $this->Ln();
        // Data
        foreach ($retouchs as $row) {
            
            $this->CellFitScale($w[0], 8, sprintf('%s %s', utf8_decode($this->translator->trans('fpdf_invoice.selected_retouch')), utf8_decode($row['retouch'])), 'LR');
            $this->CellFitScale($w[1], 8, $row['quantity'], 'LR', 0, 'C');
            $this->CellFitScale($w[2], 8, $this->concatEuro($row['price_per_unit']), 'LR', 0, 'C');
            $this->CellFitScale($w[3], 8, $row['vat_number'], 'LR', 0, 'C');
            $this->CellFitScale($w[4], 8, $this->concatEuro($row['total_price']), 'LR', 0, 'C');
            $this->Ln();
        }
        
        if (count($settings) != 0) {
            $this->Cell(array_sum($w), 0, '', 'LRBT', 10);
            $this->Ln();
        }
        
        foreach ($settings as $key => $row) {
            
            $this->CellFitScale($w[0], 8, sprintf('%s %s', utf8_decode($this->translator->trans('fpdf_invoice.selected_param')), utf8_decode($row['field_group']->getLabelText())), 'LR');
            $this->CellFitScale($w[1], 8, $row['quantity'], 'LR', 0, 'C');
            $this->CellFitScale($w[2], 8, $this->concatEuro($row['price_per_unit']), 'LR', 0, 'C');
            $this->CellFitScale($w[3], 8, $row['vat_number'], 'LR', 0, 'C');
            $this->CellFitScale($w[4], 8, $this->concatEuro($row['total_price']), 'LR', 0, 'C');
            $this->Ln();
        }
        
        // spacing
        foreach ($w as $value) {
            $this->Cell($value, (self::DETAIL_TABLE_MAX_Y - $this->GetY()) > 10 ? (self::DETAIL_TABLE_MAX_Y - $this->GetY()) : 10, ' ', 'LR', 0);
        }
        $this->Ln();
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
        $this->Ln(5);
    }

    public function setRechargeTable(Invoice $invoice)
    {
        if ($this->GetY() + 100 > $this->PageBreakTrigger) {
            $this->AddPage($this->CurOrientation);
            $this->currentX = $this->GetX();
            $this->currentY = 20;
        } else {
            // cuurent
            $this->currentX = $this->GetX();
            $this->currentY = $this->GetY();
        }
        
        $header = array($this->translator->trans('fpdf_invoice.Designation'),
            $this->translator->trans('fpdf_invoice.create_date'),
            $this->translator->trans('fpdf_invoice.unit_price'),
            $this->translator->trans('fpdf_invoice.vat'),
            $this->translator->trans('fpdf_invoice.total_wt'));
        
        $this->SetDrawColor(62, 62, 62);
        $this->SetFillColor(255);
        $this->SetLineWidth(.2);
        // Column widths
        $w = array(85, 40, 20, 20, 29);
        // Header
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 7, utf8_decode($header[$i]), 1, 0, 'C');
        }
        $this->Ln();
        // Data
        $this->CellFitScale($w[0], 8, sprintf('%s', utf8_decode($this->translator->trans('fpdf_invoice.recharge_wallet'))), 'LR');
        $this->CellFitScale($w[1], 8, $invoice->getPaymentDate()->format('d-m-Y'), 'LR', 0, 'C');
        $this->CellFitScale($w[2], 8, $this->concatEuro(round($invoice->getTotalAmount(), 2)), 'LR', 0, 'C');
        $this->CellFitScale($w[3], 8, '1', 'LR', 0, 'C');
        $this->CellFitScale($w[4], 8, $this->concatEuro(round($invoice->getTotalAmount(), 2)), 'LR', 0, 'C');
        $this->Ln();
        
        // // spacing
        foreach ($w as $value) {
            $this->Cell($value, (self::DETAIL_TABLE_MAX_Y - $this->GetY()) > 10 ? (self::DETAIL_TABLE_MAX_Y - $this->GetY()) : 10, ' ', 'LR', 0);
        }
        $this->Ln();
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
        $this->Ln(5);
    }

    public function setAdditionalTable(?array $retouchs, ?array $settings, ?array $originalFiles, ?array $settingsOld) {
        if ($this->GetY() + 100 > $this->PageBreakTrigger) {
            $this->AddPage($this->CurOrientation);
            $this->currentX = $this->GetX();
            $this->currentY = 20;
        } else {
            // cuurent
            $this->currentX = $this->GetX();
            $this->currentY = $this->GetY();
        }
        
        $header = array($this->translator->trans('fpdf_invoice.Designation'),
            $this->translator->trans('fpdf_invoice.amount'),
            $this->translator->trans('fpdf_invoice.unit_price'),
            $this->translator->trans('fpdf_invoice.vat'),
            $this->translator->trans('fpdf_invoice.total_wt'));
        
        $this->SetDrawColor(62, 62, 62);
        $this->SetFillColor(255);
        $this->SetLineWidth(.2);
        // Column widths
        $w = array(110, 15, 20, 20, 29);
        // Header
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 7, utf8_decode($header[$i]), 1, 0, 'C');
        }
        $this->Ln();

        //first line:
        $this->CellFitScale($w[0], 8, sprintf('%s', utf8_decode($this->translator->trans('fpdf_invoice.first_order_line'))) , 'LR');
        $this->CellFitScale($w[1], 8,'', 'LR', 0, 'C');
        $this->CellFitScale($w[2], 8,'', 'LR', 0, 'C');
        $this->CellFitScale($w[3], 8,'', 'LR', 0, 'C');
        $this->CellFitScale($w[4], 8,'', 'LR', 0, 'C');
        $this->Ln();

        //first line sub:
        $this->CellFitScale($w[0], 4, sprintf('%s', utf8_decode($this->translator->trans('fpdf_invoice.first_order_line_sub'))) , 'LR');
        $this->CellFitScale($w[1], 4,'', 'LR', 0, 'C');
        $this->CellFitScale($w[2], 4,'', 'LR', 0, 'C');
        $this->CellFitScale($w[3], 4,'', 'LR', 0, 'C');
        $this->CellFitScale($w[4], 4,'', 'LR', 0, 'C');
        $this->Ln();

        //space
        $this->CellFitScale($w[0], 8,'', 'LR');
        $this->CellFitScale($w[1], 8,'', 'LR', 0, 'C');
        $this->CellFitScale($w[2], 8,'', 'LR', 0, 'C');
        $this->CellFitScale($w[3], 8,'', 'LR', 0, 'C');
        $this->CellFitScale($w[4], 8,'', 'LR', 0, 'C');
        $this->Ln();

        // Data
        $this->CellFitScale($w[0], 8, sprintf('%s', utf8_decode($this->translator->trans('fpdf_invoice.selected_retouch'))), 'LR');
        $this->CellFitScale($w[1], 8,'', 'LR', 0, 'C');
        $this->CellFitScale($w[2], 8,'', 'LR', 0, 'C');
        $this->CellFitScale($w[3], 8,'', 'LR', 0, 'C');
        $this->CellFitScale($w[4], 8,'', 'LR', 0, 'C');
        $this->Ln();
        foreach ($retouchs as $row) {
            $this->CellFitScale($w[0], 8, sprintf('%s', utf8_decode($row['retouch'])), 'LR');
            $this->CellFitScale($w[1], 8, $row['quantity'], 'LR', 0, 'C');
            $this->CellFitScale($w[2], 8, $this->concatEuro($row['price_per_unit']), 'LR', 0, 'C');
            $this->CellFitScale($w[3], 8, $row['vat_number'], 'LR', 0, 'C');
            $this->CellFitScale($w[4], 8, $this->concatEuro($row['total_price']), 'LR', 0, 'C');
            $this->Ln();
        }

        $this->CellFitScale($w[0], 8, sprintf('%s', utf8_decode($this->translator->trans('fpdf_invoice.selected_retouch_paid'))), 'LR');
        $this->CellFitScale($w[1], 8,'', 'LR', 0, 'C');
        $this->CellFitScale($w[2], 8,'', 'LR', 0, 'C');
        $this->CellFitScale($w[3], 8,'', 'LR', 0, 'C');
        $this->CellFitScale($w[4], 8,'', 'LR', 0, 'C');
        $this->Ln();
        foreach ($originalFiles as $row) {
            $this->CellFitScale($w[0], 8, sprintf('%s', utf8_decode($row['retouch'])), 'LR');
            $this->CellFitScale($w[1], 8, '-' . $row['quantity'], 'LR', 0, 'C');
            $this->CellFitScale($w[2], 8, '-' . $this->concatEuro($row['price_per_unit']), 'LR', 0, 'C');
            $this->CellFitScale($w[3], 8, $row['vat_number'], 'LR', 0, 'C');
            $this->CellFitScale($w[4], 8, '-' . $this->concatEuro($row['total_price']), 'LR', 0, 'C');
            $this->Ln();
        }
        
        if (count($settings) != 0 || count($settingsOld) != 0) {
            $this->Cell(array_sum($w), 0, '', 'LRBT', 10);
            $this->Ln();
        }
        
        foreach ($settings as $key => $row) {
            
            $this->CellFitScale($w[0], 8, sprintf('%s %s', utf8_decode($this->translator->trans('fpdf_invoice.selected_param')), utf8_decode($row['field_group']->getLabelText())), 'LR');
            $this->CellFitScale($w[1], 8, $row['quantity'], 'LR', 0, 'C');
            $this->CellFitScale($w[2], 8, $this->concatEuro($row['price_per_unit']), 'LR', 0, 'C');
            $this->CellFitScale($w[3], 8, !empty($row['vat_number']) ? $row['vat_number'] : '', 'LR', 0, 'C');
            $this->CellFitScale($w[4], 8, $this->concatEuro($row['total_price']), 'LR', 0, 'C');
            $this->Ln();
        }

        foreach ($settingsOld as $key => $row) {
            
            $this->CellFitScale($w[0], 8, sprintf('%s %s', utf8_decode($this->translator->trans('fpdf_invoice.selected_param')), utf8_decode($row['field_group']->getLabelText())), 'LR');
            $this->CellFitScale($w[1], 8, '-' . $row['quantity'], 'LR', 0, 'C');
            $this->CellFitScale($w[2], 8, $this->concatEuro($row['price_per_unit']), 'LR', 0, 'C');
            $this->CellFitScale($w[3], 8, !empty($row['vat_number']) ? $row['vat_number'] : '', 'LR', 0, 'C');
            $this->CellFitScale($w[4], 8, '-' .$this->concatEuro($row['total_price']), 'LR', 0, 'C');
            $this->Ln();
        }
        
        // spacing
        foreach ($w as $value) {
            $this->Cell($value, (self::DETAIL_TABLE_MAX_Y - $this->GetY()) > 10 ? (self::DETAIL_TABLE_MAX_Y - $this->GetY()) : 10, ' ', 'LR', 0);
        }
        $this->Ln();
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
        $this->Ln(5);
    }
    
    /**
     * @param array|null $data
     * @param Transaction|null $transaction
     * @param Promo[] $promotions
     */
    public function setTaxPrice(?array $data, ?Transaction $transaction = null, array $promotions = [])
    {
        if ($this->GetY() + 50 > $this->PageBreakTrigger) {
            $this->AddPage($this->CurOrientation);
            $this->currentX = $this->GetX();
            $this->currentY = 20;
        } else {
            // current
            $this->currentX = $this->GetX();
            $this->currentY = $this->GetY();
        }
        
        $header = array($this->translator->trans('fpdf_invoice.vat'),
            $this->translator->trans('fpdf_invoice.total_wt'),
            $this->translator->trans('fpdf_invoice.rate'),
            $this->translator->trans('fpdf_invoice.total_vat'));
        
        // Column widths
        $w = array(15, 29, 15, 20);
        
        $this->setXY($this->currentX, $this->currentY);
        // Header
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 7, utf8_decode($header[$i]), 1, 0, 'C');
        }
        $this->Ln();
        // Data
        $this->CellFitScale($w[0], 6, $data['tva_number'], 'LR', 0, 'C');
        $this->CellFitScale($w[1], 6, $this->concatEuro($data['price_excluding_price']), 'LR', 0, 'C');
        $this->CellFitScale($w[2], 6, $data['tax_percentage'], 'LR', 0, 'C');
        $this->CellFitScale($w[3], 6, $this->concatEuro($data['tax_price']), 'LR', 0, 'C');
        $this->Ln();
        
        // spacing the table
        foreach ($w as $value) {
            $this->Cell($value, 13.5, ' ', 'LR', 0);
        }
        $this->Ln();
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
        $this->Ln(5);
        
        if (!is_null($transaction) && !empty($transaction->getCardBrand()) && !empty($transaction->getCardNumber())) {
            $this->setXY($this->GetX(), $this->GetY());
            $this->CellFitScale(0, 0, utf8_decode($this->translator->trans('fpdf_invoice.payed_with', ['%card_brand%' => $transaction->getCardBrand(), '%card_number%' => $transaction->getCardNumber()])));
            $this->Ln(7);
        }
        
        if (!empty($promotions)) {
            $promotionsList = [];
            foreach ($promotions as $promo) {
                if (is_null($promo)) {
                    continue;
                }
                $promotionsList[] = $promo->getPromoCode();
            }
            if (count($promotionsList) > 0){
                $this->MultiCell(0, 5, utf8_decode($this->translator->trans('fpdf_invoice.code_promo', ['%code_promo%' => implode(" ,", $promotionsList)])));
                $this->Ln(5);
            }
        }
        
        
        // $this->setXY($this->GetX(), $this->GetY());
        // $this->setFont('Times', '', 10);
        // $this->Cell(0, 0, utf8_decode($this->translator->trans('fpdf_invoice.currency')));
    }
    
    public function SetWidths($w)
    {
        //Set the array of column widths
        $this->widths = $w;
    }
    
    public function SetAligns($a)
    {
        //Set the array of column alignments
        $this->aligns = $a;
    }
    
    public function setTotalPrice(?array $data)
    {
        $this->setXY($this->currentX, $this->currentY);
        
        $this->SetWidths(array(45, 35));
        foreach ($data as $colName => $colValue) {
            $this->setX(self::TOTAL_PRICE_TABLE_X_HEIGHT);
            $this->Row(utf8_decode($colName), is_numeric($colValue) ? $this->concatEuro($colValue) : $colValue);
        }
    }
    
    // Page footer
    public function Footer()
    {
        // Position at 2 cm from bottom
        if ($this->invoiceType == 'invoice.types.additional_invoice_wallet' || $this->invoiceType == 'invoice.types.wallet_invoice') {
            $this->SetY(-38);
            $this->setFont('Times', 'B', 12);
            $this->SetTextColor(233, 50, 177);
            $this->Cell(0, 3.5, sprintf('%s', utf8_decode($this->translator->trans('fpdf_invoice.wallet_title'))), 0, 0, 'L');
            $this->Ln();

            $this->setFont('Times', 'B', 10);
            $this->SetTextColor(0, 0, 0);
            $this->SetY(-32);
            // if ($this->beforeWallet !== null && $this->afterWallet !== null){
                $this->Cell(0, 3.5, sprintf('%s: %s', utf8_decode($this->translator->trans('fpdf_invoice.before_balance')), $this->beforeWallet !== null ? $this->concatEuro($this->beforeWallet) : 'N/A', 0, 0, 'L'));
                $this->Ln();

                $this->SetY(-25);
                $this->Cell(0, 3.5, sprintf('%s: %s', utf8_decode($this->translator->trans('fpdf_invoice.after_balance')), $this->afterWallet !== null ? $this->concatEuro($this->afterWallet) : 'N/A', 0, 0, 'L'));
                $this->Ln();
            // }
            $this->SetY(-8);
        }
        elseif ($this->invoiceType == 'invoice.types.organization_monthly' || $this->invoiceType == 'invoice.types.user_monthly') {
            $this->SetY(-40);
            $this->setFont('Times', 'B', 12);
            $this->SetTextColor(233, 50, 177);
            $this->Cell(0, 3.5, sprintf('%s', utf8_decode($this->translator->trans('fpdf_invoice.footer_prev_title'))), 0, 0, 'C');
            $this->Ln();

            $this->setFont('Times', 'B', 10);
            $this->SetTextColor(54, 54, 54);
            $this->SetY(-35);
            $this->Cell(0, 3.5, sprintf('%s', utf8_decode($this->translator->trans('fpdf_invoice.footer_prev_sub'))), 0, 0, 'L');
            $this->Ln();

            $this->SetY(-31);
            $this->setFont('Times', '', 10);
            $this->SetTextColor(85, 85, 85);
            $this->Cell(0, 3.5, sprintf('%s', utf8_decode($this->translator->trans('fpdf_invoice.footer_prev_sub_second'))), 0, 0, 'L');
            $this->Ln();

            $this->setFont('Times', '', 9.8);
            $this->SetTextColor(85, 85, 85);
            $this->MultiCell(0, 3.5, sprintf('%s', iconv('UTF-8', 'Windows-1252', $this->translator->trans('fpdf_invoice.footer_prev'))), 0, 'L');
            // $this->MultiCell(0, 3.5, sprintf('%s', utf8_decode($this->translator->trans('fpdf_invoice.footer_prev', ['%euros%' => iconv('UTF-8', 'Windows-1252', "€")]))), 0, 'L');
            $this->Ln();
            $this->SetY(-12);
        }
        elseif ($this->invoiceType == 'invoice.types.additional_invoice')
        {
           $this->SetY(-46);
           $this->setFont('Times', 'B', 12);
           $this->SetTextColor(233, 50, 177);
           $this->Cell(0, 3.5, sprintf('%s', utf8_decode($this->translator->trans('fpdf_invoice.footer_prev_title'))), 0, 0, 'L');
           $this->Ln();

           $this->setFont('Times', 'B', 10);
           $this->SetTextColor(0, 0, 0);
           $this->SetY(-38);
           $this->Cell(0, 3.5, sprintf('%s', utf8_decode($this->translator->trans('fpdf_invoice.footer_prev_sub'))), 0, 0, 'C');
           $this->Ln();

           $this->SetY(-31);
           $this->Cell(0, 3.5, sprintf('%s', utf8_decode($this->translator->trans('fpdf_invoice.footer_prev_sub_second'))), 0, 0, 'C');
           $this->Ln();

           $this->setFont('Times', '', 9.8);
           $this->MultiCell(0, 3.5, sprintf('%s', iconv('UTF-8', 'Windows-1252', $this->translator->trans('fpdf_invoice.footer_prev'))), 0, 'C');
//           $this->MultiCell(0, 3.5, sprintf('%s', utf8_decode($this->translator->trans('fpdf_invoice.footer_prev', ['%euros%' => iconv('UTF-8', 'Windows-1252', "€")]))), 0, 'L');
           $this->Ln();
           $this->SetY(-12);
        }
        else {
            $this->SetY(-12);
        }
        
        $this->setFont('Times', '', 8.5);
        $this->SetTextColor(105, 105, 105);
        $this->Cell(0, 3.5, sprintf('EMMOBILIER %s', utf8_decode($this->translator->trans('fpdf_invoice.footer1'))), 0, 0, 'C');
        $this->Ln();
        $this->Cell(0, 3.5, utf8_decode($this->translator->trans('fpdf_invoice.footer2')), 0, 0, 'C');
        $this->Ln();
        
        $this->SetTextColor(233, 50, 177);
        $this->Cell(180, 3.5, utf8_decode("www.emmobilier.fr"), 0, 0, 'C');
    }
    
    public function concatEuro($str)
    {
        return $str . iconv('UTF-8', 'Windows-1252', "€");
    }
    
    public function Row($title, $content)
    {
        //Calculate the height of the row
        $nb = 0;
        $nb = max($nb, $this->NbLines($this->widths[0], $title));
        $nb = max($nb, $this->NbLines($this->widths[1], $content));
        
        $h = 8.5 * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        $w = $this->widths[0];
        //Save the current position
        $x = $this->GetX();
        $y = $this->GetY();
        //Draw the border
        $this->Rect($x, $y, $w, $h);
        //Print the text
        $this->SetFont('Times', 'B', 12);
        $this->MultiCell($w, 8.5, $title, 0, 'L');
        //Put the position to the right of the cell
        $this->SetXY($x + $w, $y);
        
        $w = $this->widths[1];
        //Save the current position
        $x = $this->GetX();
        $y = $this->GetY();
        //Draw the border
        $this->Rect($x, $y, $w, $h);
        //Print the text
        $this->SetFont('Times', '', 12);
        $this->MultiCell($w, 8.5, $content, 0, 'C');
        //Put the position to the right of the cell
        $this->SetXY($x + $w, $y);
        
        //Go to the next line
        $this->Ln($h);
    }
    
    public function NbLines($w, $txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw =& $this->CurrentFont['cw'];
        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n") {
            $nb--;
        }
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
            }
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }
        return $nl;
    }
    
    //Cell with horizontal scaling if text is too wide
    function CellFit($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $scale = false, $force = true)
    {
        //Get string width
        $str_width = $this->GetStringWidth($txt);
        
        //Calculate ratio to fit cell
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $ratio = @(($w - $this->cMargin * 2) / $str_width);
        
        $fit = ($ratio < 1 || ($ratio > 1 && $force));
        if ($fit) {
            if ($scale) {
                //Calculate horizontal scaling
                $horiz_scale = $ratio * 100.0;
                //Set horizontal scaling
                $this->_out(sprintf('BT %.2F Tz ET', $horiz_scale));
            } else {
                //Calculate character spacing in points
                $char_space = ($w - $this->cMargin * 2 - $str_width) / max($this->GetStringWidth($txt) - 1, 1) * $this->k;
                //Set character spacing
                $this->_out(sprintf('BT %.2F Tc ET', $char_space));
            }
            //Override user alignment (since text will fill up cell)
            $align = '';
        }
        
        //Pass on to Cell method
        $this->Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
        
        //Reset character spacing/horizontal scaling
        if ($fit)
            $this->_out('BT ' . ($scale ? '100 Tz' : '0 Tc') . ' ET');
    }
    
    //Cell with horizontal scaling only if necessary
    function CellFitScale($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '')
    {
        $this->CellFit($w, $h, $txt, $border, $ln, $align, $fill, $link, true, false);
    }
    
    public function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger) {
            $this->AddPage($this->CurOrientation);
        }
    }

    /**
     * Get the value of beforeWallet
     */ 
    public function getBeforeWallet()
    {
        return $this->beforeWallet;
    }

    /**
     * Set the value of beforeWallet
     *
     * @return  self
     */ 
    public function setBeforeWallet($beforeWallet)
    {
        $this->beforeWallet = $beforeWallet;

        return $this;
    }

    /**
     * Get the value of afterWallet
     */ 
    public function getAfterWallet()
    {
        return $this->afterWallet;
    }

    /**
     * Set the value of afterWallet
     *
     * @return  self
     */ 
    public function setAfterWallet($afterWallet)
    {
        $this->afterWallet = $afterWallet;

        return $this;
    }
}
