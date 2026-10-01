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
     * @param string $column Record field to return, `host` by default, `ip` for A records
     * @return null|string[]
     */
    public function getDomainRecords($domain, $type, $column = 'host')
    {
        $records = @dns_get_record($domain, $type);

        if ($records === false) {
            return null;
        }

        return array_column($records, $column);
    }

    /**
     * The IPv4 addresses a host resolves to. An IP address resolves to itself.
     *
     * @param string $host
     * @return string[]
     */
    public function getAddresses($host)
    {
        if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return [$host];
        }

        $records = $this->getDomainRecords($host, DNS_A, 'ip');

        return $records ?: [];
    }

    /**
     * Whether the domain points at the target host (A records share an address).
     *
     * @param string $domain
     * @return boolean
     */
    public function hasProperRecord($domain)
    {
        $domainAddresses = $this->getAddresses($domain);

        if (!count($domainAddresses)) {
            return false;
        }

        return count(array_intersect($domainAddresses, $this->getAddresses($this->targetHost))) > 0;
    }
}
