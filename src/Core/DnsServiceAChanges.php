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
     * @return string|null
     */
    public function getDomainA($domain)
    {
        $records = dns_get_record($domain, DNS_A);
        if (!$records) return null;
        return $records[0]['target'];
    }

    /**
     * @param string $domain
     * @return boolean
     */
    public function hasProperCNAME($domain)
    {
        $domainCname = $this->getDomainCNAME($domain);
        if ($domainCname) return $domainCname === $this->targetCname;

        return (bool) array_intersect(
            $this->getDomainA($domain),
            $this->getDomainA($this->targetCname)
        );
    }