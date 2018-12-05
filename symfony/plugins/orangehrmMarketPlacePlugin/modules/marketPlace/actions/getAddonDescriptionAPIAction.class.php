<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA 02110-1301, USA
 */

/**
 * Class getAddonDescriptionAPIAction
 */
class getAddonDescriptionAPIAction extends baseAddonAction
{
    /**
     * @param $request
     */
    public function execute($request)
    {
        $addonDescription = 'PHA+c29tZSBjb3VudHJpZXMgKGUuZy4sIHRoZSBVbml0ZWQgU3RhdGVzIGFuZCBDYW5hZGEpLCBlc3NheXMgaGF2ZSBiZWNvbWUgYSBtYWpvciBwYXJ0IG9mIGZvcm1hbCBlZHVjYXRpb24uCiBTZWNvbmRhcnkgc3R1ZGVudHMgYXJlIHRhdWdodCBzdHJ1Y3R1cmVkIGVzc2F5IGZvcm1hdHMgdG8gaW1wcm92ZSB0aGVpciB3cml0aW5nIHNraWxsczsgYWRtaXNzaW9uIGVzc2F5cyBhcmUgb2Z0ZW4KIHVzZWQgYnkgdW5pdmVyc2l0aWVzIGluIHNlbGVjdGluZyBhcHBsaWNhbnRzLCBhbmQgaW4gdGhlIGh1bWFuaXRpZXMgYW5kIHNvY2lhbCBzY2llbmNlcyBlc3NheXMgYXJlIG9mdGVuIHVzZWQgYXMgYSB3YXkgb2YgYXNzZXNzaW5nIHRoZQogcGVyZm9ybWFuY2Ugb2Ygc3R1ZGVudHMgZHVyaW5nIGZpbmFsIGV4YW1zLjwvcD48YnI+CiA8cD5zb21lIGNvdW50cmllcyAoZS5nLiwgdGhlIFVuaXRlZCBTdGF0ZXMgYW5kIENhbmFkYSksIGVzc2F5cyBoYXZlIGJlY29tZSBhIG1ham9yIHBhcnQgb2YgZm9ybWFsIGVkdWNhdGlvbi4KIFNlY29uZGFyeSBzdHVkZW50cyBhcmUgdGF1Z2h0IHN0cnVjdHVyZWQgZXNzYXkgZm9ybWF0cyB0byBpbXByb3ZlIHRoZWlyIHdyaXRpbmcgc2tpbGxzOyBhZG1pc3Npb24gZXNzYXlzIGFyZSBvZnRlbgogdXNlZCBieSB1bml2ZXJzaXRpZXMgaW4gc2VsZWN0aW5nIGFwcGxpY2FudHMsIGFuZCBpbiB0aGUgaHVtYW5pdGllcyBhbmQgc29jaWFsIHNjaWVuY2VzIGVzc2F5cyBhcmUgb2Z0ZW4gdXNlZCBhcyBhIHdheSBvZiBhc3Nlc3NpbmcgdGhlCiBwZXJmb3JtYW5jZSBvZiBzdHVkZW50cyBkdXJpbmcgZmluYWwgZXhhbXMuPC9wPjxicj4KIDxwPnNvbWUgY291bnRyaWVzIChlLmcuLCB0aGUgVW5pdGVkIFN0YXRlcyBhbmQgQ2FuYWRhKSwgZXNzYXlzIGhhdmUgYmVjb21lIGEgbWFqb3IgcGFydCBvZiBmb3JtYWwgZWR1Y2F0aW9uLgogU2Vjb25kYXJ5IHN0dWRlbnRzIGFyZSB0YXVnaHQgc3RydWN0dXJlZCBlc3NheSBmb3JtYXRzIHRvIGltcHJvdmUgdGhlaXIgd3JpdGluZyBza2lsbHM7IGFkbWlzc2lvbiBlc3NheXMgYXJlIG9mdGVuCiB1c2VkIGJ5IHVuaXZlcnNpdGllcyBpbiBzZWxlY3RpbmcgYXBwbGljYW50cywgYW5kIGluIHRoZSBodW1hbml0aWVzIGFuZCBzb2NpYWwgc2NpZW5jZXMgZXNzYXlzIGFyZSBvZnRlbiB1c2VkIGFzIGEgd2F5IG9mIGFzc2Vzc2luZyB0aGUKIHBlcmZvcm1hbmNlIG9mIHN0dWRlbnRzIGR1cmluZyBmaW5hbCBleGFtcy48L3A+';
        $this->addonDescription = base64_decode($addonDescription);
    }
}
