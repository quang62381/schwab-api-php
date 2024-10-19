# schwab-api
A library to connect to the Charles Schwab API


Setup your Schwab developer account here.
Create a new Schwab individual developer app with callback url "https://127.0.0.1" (case sensitive)
Add both API products to your app: "Accounts and Trading Production" and "Market Data Production".
Wait until the status is "Ready for use", note that "Approved - Pending" will not work.
Enable TOS (Thinkorswim) for your Schwab account, it is needed for orders and other api calls.


To make recovery in case of failure easier, an additional sshd will
be started on port '1022'. If anything goes wrong with the running
ssh you can still connect to the additional one.
If you run a firewall, you may need to temporarily open this port. As
this is potentially dangerous it's not done automatically. You can
open the port with e.g.:
'iptables -I INPUT -p tcp --dport 1022 -j ACCEPT'


Third party sources disabled

Some third party entries in your sources.list were disabled. You can
re-enable them after the upgrade with the 'software-properties' tool
or your package manager.


Checking package manager
Reading package lists... Done
Building dependency tree
Reading state information... Done

Invalid package information

After updating your package information, the essential package
'ubuntu-minimal' could not be located. This may be because you have
no official mirrors listed in your software sources, or because of
excessive load on the mirror you are using. See /etc/apt/sources.list
for the current list of configured software sources.
In the case of an overloaded mirror, you may want to try the upgrade
again later.


Restoring original system state

Aborting
      g package lists... 2%
*** Collecting problem information

The collected information can be sent to the developers to improve the
application. This might take a few minutes.
Reading package lists... Done
Building dependency tree
Reading state information... Done
.
=== Command terminated with exit status 1 (Sat Oct 12 16:58:30 2024) ===
