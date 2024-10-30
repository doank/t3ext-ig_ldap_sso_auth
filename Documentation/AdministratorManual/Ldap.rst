.. _admin-manual-ldap:

LDAP
----

The second tab is the global configuration about a single LDAP server.

.. only:: html

	**Sections:**

	.. toctree::
		:local:
		:depth: 1


.. _admin-manual-ldap-server:

Server
^^^^^^

Choose your LDAP type (OpenLDAP or Active Directory). This is used internally to
follow (or not) referrals returned by the LDAP server and to help you with
suggested mapping configuration.


.. _admin-manual-ldap-characterset:

Character set
^^^^^^^^^^^^^

Character set of your LDAP connection. Usually ``utf-8``.


.. _admin-manual-ldap-host:

Host
^^^^

Host of your LDAP. You may use either a host name / IP address or prefix it with
a protocol such as ``ldap://<hostname>`` or ``ldaps://<hostname>`` (latter in
case you want to connect with SSL).


.. _admin-manual-ldap-port:

Port
^^^^

Port your LDAP uses. Default LDAP ports are 389 (``ldap://``) and 636
(``ldaps://``).


.. _admin-manual-ldap-tls:

TLS
^^^

Whether you want to use :abbr:`TLS (Transport Layer Security)`, that is
typically start with an connection on default port 389 and then set up an
encrypted connection.

.. note::

   More information on TLS may be found at http://www.openldap.org/doc/admin24/tls.html.


.. _admin-manual-ldap-ssl:

SSL
^^^

Whether you want to use :abbr:`SSL (Secure Socket Layer)`, that is start with an
encrypted connection on default port 636.

.. note::

    Some web servers may fail at connecting to the LDAP server since they report
    that the server certificate is untrusted (although issued by a valid CA such
    as Letsencrypt). In case this happens and you cannot change the web server
    configuration (e.g., shared hosting), you may add this line to
    :file:`typo3conf/AdditionalConfiguration.php`:

    .. code-block:: php

        // Always trust the LDAP server certificate
        putenv('LDAPTLS_REQCERT=never');


.. _admin-manual-ldap-binddn:

Bind DN
^^^^^^^

:term:`DN` of the LDAP user you will use to connect to the LDAP server. The
:term:`DN` is composed of a series of :abbr:`RDN (Relative Distinguished Names)`'s
which are the unique (or unique'ish) attributes at each level in the
:term:`DIT`. The following diagram illustrates building up the DN from the
RDN's.

.. figure:: ../Images/dit-dn-rdn.png
	:alt: DN is the sum of all RDNs

	Building up the DN (Distinguished Name) from the RDN's (Relative
	Distinguished Names)

**Example:**

::

	cn=Robert Smith,ou=people,dc=example,dc=com

.. note::

	Your LDAP user needs to be granted access to the directory where users and
	groups are stored and full read access to users and groups for all attributes
	you plan to fetch.

	When connecting to an Active Directory, this corresponds to a user account
	that has privileges to search for users. E.g.,
	``CN=Administrator,CN=Users,DC=mycompany,DC=com``. This user account must
	have at least domain user privileges.

.. note::

    On a Windows Server, you may find the DN of a given user using a command
    prompt::

        dsquery user -name <known username>

    Example: If you are searching for all users named "John", you can enter the$
    username as ``John*`` to get a list of all users whose name is John. The
    result will look like::

        "CN=John.Smith,CN=Users,DC=MyDomain,DC=com"

    Similarly, you can find the Group Base DN using::

        dsquery group -name <known group name>


.. _admin-manual-ldap-password:

Password
^^^^^^^^

This password is the same password used in association with the :term:`Bind DN`
user account to connect to the LDAP server.
