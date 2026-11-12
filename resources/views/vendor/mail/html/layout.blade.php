<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="es">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="color-scheme" content="light dark">
<meta name="supported-color-schemes" content="light dark">
<style>
@media only screen and (max-width: 620px) {
.inner-body, .footer {
width: 100% !important;
}

.header {
padding: 32px 24px 32px !important;
}

.content-cell {
padding: 0 24px 44px !important;
}

.footer-pad {
padding: 0 24px 40px !important;
}

h1 {
font-size: 30px !important;
}

.codigo-valor {
font-size: 44px !important;
letter-spacing: 4px !important;
}

.dato-etiqueta {
width: 36% !important;
}
}

@media only screen and (max-width: 500px) {
.button {
display: block !important;
text-align: center !important;
}
}

@media (prefers-color-scheme: dark) {
body, .wrapper, .inner-body {
background-color: #000000 !important;
}

h1, h2, h3, p, strong, .dato-valor, .aviso-texto, .table td {
color: #f5f5f7 !important;
}

p.lead, p.sub, .dato-etiqueta, .codigo-etiqueta {
color: #a1a1a6 !important;
}

.dato-etiqueta, .dato-valor, .dato-ultima {
border-color: #38383a !important;
}

.aviso-titulo, .codigo-valor {
color: #ff9f5a !important;
}

.button-primary, .button-blue {
background-color: #ffffff !important;
color: #002e71 !important;
}

.footer-band {
background-color: #1c1c1e !important;
}

.footer p, .footer a {
color: #a1a1a6 !important;
}

.logo-light {
display: none !important;
}

.logo-dark {
display: block !important;
max-height: none !important;
}
}
</style>
</head>
<body>

<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="center">
<table class="inner-body" align="center" width="600" cellpadding="0" cellspacing="0" role="presentation">
{{ $header ?? '' }}

<!-- Email Body -->
<tr>
<td class="content-cell">
{{ Illuminate\Mail\Markdown::parse($slot) }}

{{ $subcopy ?? '' }}
</td>
</tr>
</table>

{{ $footer ?? '' }}
</td>
</tr>
</table>
</body>
</html>
