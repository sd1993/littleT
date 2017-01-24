<?php  
/**
 * 导出文件
 * @return string
 */
	
    $file_name   = "成绩单-".date("Y-m-d H:i:s",time());
    $file_suffix = "xls";
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=$file_name.$file_suffix");
    //根据业务，自己进行模板赋值。
   

?>
<html xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:x="urn:schemas-microsoft-com:office:excel"
xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv=Content-Type content="text/html; charset=utf-8">
<meta name=ProgId content=Excel.Sheet>
<meta name=Generator content="Microsoft Excel 11">
</head>
<body>
<table border=1 cellpadding=0 cellspacing=0 width="100%" >
     <tr>
         <td colspan="5" align="center">
             <h2>成绩单</h2>
         </td>
     </tr>
     <tr>
         <td style='width:54pt' align="center">编号</td>
         <td style='width:54pt' align="center">姓名</td>
         <td style='width:54pt' align="center">语文</td>
         <td style='width:54pt' align="center">数学</td>
         <td style='width:54pt' align="center">英语</td>
     </tr>
     <tr>
        <td align="center">1</td>
        <td style="background-color: #00CC00;" align="center">Jone</td>
        <td style="background-color: #00adee;" align="center">90</td>
        <td style="background-color: #00CC00;" align="center">85</td>
        <td style="background-color: #00adee;" align="center">100</td>
     </tr>
    <tr>
        <td align="center">2</td>
        <td style="background-color: #00CC00;" align="center">Tom</td>
        <td style="background-color: #00adee;" align="center">99</td>
        <td style="background-color: #00CC00;" align="center">85</td>
        <td style="background-color: #00adee;" align="center">80</td>
    </tr>
</table>
</body>
</html>
