<?php

$regex = "%^(http://|https://|//)(";
$regex .= "www.google.|";
$regex .= "google.|";
$regex .= "w.soundcloud.|";
$regex .= "www.youtube.|";
$regex .= "youtube.|";
$regex .= "player.vimeo.com/video/|";
$regex .= 'mapbuildr.com/frame/';
$regex .= ")%";

//$regex = "%^http://player.vimeo.com/video/%";

return [
    'active' => true,
    'settings' => [
        'default' => [
            'Cache.SerializerPath' => storage_path('purifier'),
            'HTML.Doctype' => 'XHTML 1.0 Transitional',
            'HTML.DefinitionID' => '1',
            'HTML.AllowedComments' => array('pagebreak'),
            'HTML.Allowed' => 'h1[class],h2[class],h3[class],h4[class],h5[class],h6[class],hr,div[class],b,strong,i,em,span[class|style],a[href|title|class|id|target],ul,ol,li,p[class],br,img[alt|src|class],dl[class],dt[class],dd[class],iframe[width|height|src|frameborder|class],table[id|class],tbody,td[id|class],tfoot,th[id|class],thead,tr[id|class],blockquote,code,sub,sup,pre,header[id|class],footer[id|class],article[id|class],section[id|class],del',
            'CSS.AllowedProperties' => 'font-size,font-weight,font-style,text-decoration,text-align,margin-left,margin-right',
            'Attr.AllowedFrameTargets' => array('_blank', '_self', '_parent', '_top'),
            'AutoFormat.AutoParagraph' => true,
            'AutoFormat.RemoveEmpty' => true,
            "HTML.SafeIframe" => 'true',
            "URI.SafeIframeRegexp" => $regex,
            "URI.AllowedSchemes" => array(
                'http' => true,
                'https' => true,
                'mailto' => true,
                'ftp' => true,
                'nntp' => true,
                'news' => true,
                'tel' => true,
            )
        ]
    ],
];
