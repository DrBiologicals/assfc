<?php
$pageNavi = 'Contact Us';
include 'header.php';
?>
<?php include 'navbar.php'; ?>
<div id="main" >
    <form action="contact.php" method="post">
        <table cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td>
                    Name:                    
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="name" style="width: 290px;border-width:3px;" border="1">
                </td>
                <td></td>
                <td></td>
                <td>
                    <center>
                        Alternatively you can contact a HERA verified member on either:
                    </center>
                </td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td>
                    Email:                    
                </td>
                <td></td>
                <td></td>
                <td>
                    <center>
                        Telephone: 09-2838-12939
                    </center>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="email" style="width: 290px;border-width:3px;" border="1">
                </td>
                <td></td>
                <td></td>
                <td>
                    <center>
                        Email: John.doe@Hera-verified.co.nz
                    </center>
                </td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td>
                    Subject:                    
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="subject" style="width: 290px;border-width:3px;" border="1">
                </td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td>
                    Message:                    
                </td>
            </tr>
            <tr>
                <td style="overflow: hidden;display: inline-block;white-space: nowrap;">
                    <textarea rows="10" cols="35" name="message" style="border-width:3px;" border="1"></textarea>
                </td>
                <td></td>
                <td></td>
                <td>
                <center>
                    <iframe width="250" height="200" frameborder="0" scrolling="no" marginheight="0" style="border-width:3px;" border="1"
                            marginwidth="0" src="https://maps.google.co.nz/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=17-19+Gladding+Place,+Auckland&amp;aq=0&amp;oq=17-19+gladdin&amp;sll=-36.989702,174.885961&amp;sspn=0.001296,0.002411&amp;t=h&amp;ie=UTF8&amp;hq=&amp;hnear=17%2F19+Gladding+Pl,+Manukau,+Auckland+2104&amp;ll=-36.989594,174.88558&amp;spn=0.002592,0.004823&amp;z=14&amp;output=embed">                    
                    </iframe><br /><small><a href="https://maps.google.co.nz/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q=17-19+Gladding+Place,+Auckland&amp;aq=0&amp;oq=17-19+gladdin&amp;sll=-36.989702,174.885961&amp;sspn=0.001296,0.002411&amp;t=h&amp;ie=UTF8&amp;hq=&amp;hnear=17%2F19+Gladding+Pl,+Manukau,+Auckland+2104&amp;ll=-36.989594,174.88558&amp;spn=0.002592,0.004823&amp;z=14" style="color:#0000FF;text-align:left">View Larger Map</a></small>
                </center>  
                </td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td>
                    <input type="submit" name="send" value="Send" style="width:75px;height: 40px;float:right;border-width:3px;" border="1">
                </td>
            </tr>
        </table>
    </form>  
</div>
<?php include 'footer.php'; ?>