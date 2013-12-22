<?php
/**
 * Debranova copyright message placeholder
 *
 * @category    Member
 * @package     Entity
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 Debranova
 */
namespace Member\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MemberInvoiceAdvance
 *
 * @ORM\Table(name="member_invoice_advance")
 * @ORM\Entity
 */
class InvoiceAdvance
{
    /**
     * @var integer
     *
     * @ORM\Column(name="member_invoice_advance_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $memberInvoiceAdvanceId;

    /**
     * @var float
     *
     * @ORM\Column(name="amount_invoiced", type="decimal", nullable=false)
     */
    private $amountInvoiced;

    /**
     * @var \Invoice
     *
     * @ORM\ManyToOne(targetEntity="Invoice")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="invoice_id", referencedColumnName="invoice_id")
     * })
     */
    private $invoice;

    /**
     * @var \Member
     *
     * @ORM\ManyToOne(targetEntity="Member")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="member_id", referencedColumnName="member_id")
     * })
     */
    private $member;
}
