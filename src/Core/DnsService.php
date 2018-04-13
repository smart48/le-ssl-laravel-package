<?php

namespace Imagewize\SslManager\Core;

class DnsService
{
    public function getDomainCNAME($domain)
    {
        $records = dns_get_record($domain, DNS_CNAME);

        if (!$records) return null;

        return $records[0]['target'];
    }
}
