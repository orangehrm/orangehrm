<?xml version="1.0" encoding="UTF-8"?>
<!--
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
 * Boston, MA  02110-1301, USA
 *
 */
-->
<!DOCTYPE xsl:stylesheet  [
	<!ENTITY nbsp   "&#160;">
	<!ENTITY copy   "&#169;">
	<!ENTITY reg    "&#174;">
	<!ENTITY trade  "&#8482;">
	<!ENTITY mdash  "&#8212;">
	<!ENTITY ldquo  "&#8220;">
	<!ENTITY rdquo  "&#8221;">
	<!ENTITY pound  "&#163;">
	<!ENTITY yen    "&#165;">
	<!ENTITY euro   "&#8364;">
]>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="UTF-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"/>
<xsl:template match="/report">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="{Wroot}/themes/beyondT/icons/exceptions/{type}.png" rel="icon" type="image/png"/>
<title>System was brought to a hault</title>
</head>
<style type="text/css">
	.bod {
		background: #EEEEEE;
		width:auto;
		height:auto;
	}
	#body {
		font-family:Arial, Helvetica, sans-serif;
		padding: 10px;
		width:80%;
		min-width: 700px;
		margin: auto;
		background: url(<xsl:value-of select="Wroot"/>/themes/beyondT/pictures/orange_new_02.gif) no-repeat right top;
		background-color: #FFFFFF;
		display:block;
		min-height:75px;
		position:relative;
	}

	#footer {
		font-size: 10px;
		color: #999999;
		vertical-align: bottom;
		position:fixed;
		bottom: 0;
		left:auto;
	}

	.code {
		font-family:"Courier New", Courier, monospace;
		color:#555555;
	}

	a {
		color: #FF9900;
		text-decoration: none;
	}

	.environment ul {
		list-style-image: url(<xsl:value-of select="Wroot"/>/themes/beyondT/icons/16-circle-orange.png);
	}

	.environment h3 {
		padding-left: 26px;
		background: url(<xsl:value-of select="Wroot"/>/themes/beyondT/icons/24-settings-orange.png) no-repeat left center;
	}

	.diagnosis h3 {
		padding-left: 26px;
		background: url(<xsl:value-of select="Wroot"/>/themes/beyondT/icons/24-tool-c.png) no-repeat left center;
	}

	h2 {
		padding-left: 26px;
		background: url(<xsl:value-of select="Wroot"/>/themes/beyondT/icons/exceptions/24-<xsl:value-of select="type"/>.png) no-repeat left center;
	}
</style>
<script language="javascript">
	parent.scrollTo(0, 0);
</script>
<body>
	<div id="body">
		<h2>
		<xsl:value-of select="heading"/>
		</h2>

		<p class="diagnosis">
			<xsl:value-of select="message"/>
		</p>

		<p class="diagnosis">
	  	<h3>Technical Details</h3>
			<xsl:for-each select="cause">
				<xsl:value-of select="message"/>&nbsp;
			</xsl:for-each>
		</p>
		<p class="environment">
	  		<h3>System Environment</h3>
	  		<ul>
			<xsl:for-each select="environment/version">
				<li id="{@type}" ><xsl:value-of select="@description" /> : <xsl:value-of select="."/></li>
			</xsl:for-each>
			<xsl:for-each select="environment/info">
				<li id="{@type}" ><xsl:value-of select="@description" /> : <xsl:value-of select="."/></li>
			</xsl:for-each>
			</ul>
		</p>
		<cite>
		<h5>Please note</h5>
		The error was logged in the OrangeHRM log located in <span class="code"><xsl:value-of select="logPath"/>logDB.txt</span></cite>
		<p>If you are unabled to resolve the problem please post the problem in <a href="http://www.orangehrm.com/forum/">OrangeHRM Forum</a></p>
	</div>
</body>
</html>
</xsl:template>
</xsl:stylesheet>