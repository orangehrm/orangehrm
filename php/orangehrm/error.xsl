<?xml version="1.0" encoding="iso-8859-1"?><!-- DWXMLSource="test.xml" --><!DOCTYPE xsl:stylesheet  [
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
<xsl:output method="html" encoding="iso-8859-1" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"/>
<xsl:template match="/report">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
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
</style>
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
		<cite>
		<h5>Please note</h5>
		The error was logged in the OrangeHRM log located in <span class="code"><xsl:value-of select="root"/>/lib/logs/logDB.txt</span></cite>
		<p>If you are unabled to resolve the problem please feel free to contact us on <a href="mailto:mailsupport@orangehrm.com">mailsupport@orangehrm.com</a></p>
	</div>
</body>
</html>
</xsl:template>
</xsl:stylesheet>