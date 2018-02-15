<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{SLOT="page_title"}</title>
    <link rel="stylesheet" href="/front/pts.css">
</head>
<body>
<div id="main">
    <div id="header">
        <a href="/"><span>{CV="app_header"}</span></a>
        {SLOT="action_bar"}
    </div>
    {SLOT="map"}
    <div id="clear">
    </div>

    <div id="menu">
        {SLOT="menu"}
    </div>

    <div id="content">
        {SLOT="content"}
    </div>

    <div id="news">
        {SLOT="right_bar"}
    </div>

    <div id="clear">
    </div>
    <div id="footer"></div>
</div>

</body>
    {SLOT="js"}
</html>