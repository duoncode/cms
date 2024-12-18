DELETE FROM cms.onetimetokens
WHERE
	usr = :usr
	AND token = :token;
