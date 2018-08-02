<?php

namespace Imagewize\SslManager\Core;

class DnsService
{
    /**
     * @var string
     */
    private $targetHost;

    /**
     * DnsService constructor.
     *
     * @param string $targetHost
     */
    public function __construct($targetHost)
    {
        $this->targetHost = $targetHost;
    }

    /**
     * @param string $domain
     * @param int $type
     * @return null|string[]
     */
    public function getDomainRecords($domain, $type)
    {
        $records = dns_get_record($domain, $type);

        if ($records === false) {
            return null;
        }

        return array_column($records, 'host');
    }

    /**
     * @param string $domain
     * @return boolean
     */
    public function hasProperRecord($domain)
    {
        $anameRecords = $this->getDomainRecords($domain, DNS_A);

        if ($anameRecords !== null && count($anameRecords)) {
            return array_search($this->targetHost, $anameRecords) !== false;
        }

        return (bool) array_intersect(
            $this->getDomainRecords($domain, DNS_A),
            $this->getDomainRecords($this->targetHost, DNS_A)
        );
    }
}
