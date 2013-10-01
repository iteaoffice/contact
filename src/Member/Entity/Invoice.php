<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * MemberInvoice
 *
 * @ORM\Table(name="member_invoice")
 * @ORM\Entity
 */
class MemberInvoice
{
    /**
     * @var integer
     *
     * @ORM\Column(name="member_invoice_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $memberInvoiceId;

    /**
     * @var integer
     *
     * @ORM\Column(name="year", type="integer", nullable=false)
     */
    private $year;

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
