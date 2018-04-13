<?php

namespace Imagewize\SslManager\Core;

class DnsService
{
    /**
     * @var string
     */
    private $targetCname;

    /**
     * DnsService constructor.
     *
     * @param string $targetCname
     */
    public function __construct($targetCname)
    {
        $this->targetCname = $targetCname;
    }

    /**
     * @param string $domain
     * @return string|null
     */
    public function getDomainCNAME($domain)
    {
        $records = dns_get_record($domain, DNS_CNAME);

        if (!$records) return null;

        return $records[0]['target'];
    }

    /**
     * @param string $domain
     * @return boolean
     */
    public function hasProperCNAME($domain)
    {
        return $this->getDomainCNAME($domain) === $this->targetCname;
    }
}
