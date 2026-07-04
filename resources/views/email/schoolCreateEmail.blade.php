
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Template | Payment </title>
    <link href="" rel="stylesheet"> 
</head>
<body style="margin:0; padding:0; font-family: 'Cabin', sans-serif;">
    <div class="email-container" style="background-color: #fff;">
        <table class="table-content" style="box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px; padding: 45px 30px 34px 30px ;  margin: auto; width: 600px;">
            <tbody>
                <tr>
                    <td>
                        <div class="inner-content">
                           <div class="inner-content-top">
                                 <!-- Logo -->
                                <div class="header_logo" style="display: flex; align-items: center; " >
                                    <div class="logo" >
                                        <a href="#"><img src="{{ asset('assets/uploads/logo/'.get_settings('front_logo')) }}" class="mx-4 my-5" width="200px"
                                            alt="..."></a> 
                    
                                    </div>
                                    <p style="font-size: 15px; font-weight: 500; color: #7B7F84;">{{ date('Y-m-d') }}</p>
                                </div>
                                <div class="feature-item" style="margin-top:30px; ">
                                     <div class="feature-text">
                                        <p style="color:#0C141D; font-size: 24px; font-weight:600; text-align: center;">Welcome to {{ get_settings('system_name')}}</p>
                                     </div>
                                     <div style="display: flex; align-items: center; ">
                                         <div>
                                            <h3 style="font-size: 17px; color: #0C141D; margin: 0; margin-top: 20px;"> Hi {{$data['admin_name']}},</>  
                                            <p style="font-size: 15px; color: #7B7F84; margin: 0; margin-top: 6px;">Your School has been successfully created.</p>
                                        </div>
                                         
                                     </div>
                                </div>
                           </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table style="border:1px solid #E4E7EC; margin-top: 15px;" cellpadding="20" cellspacing="0" width="600" id="emailContainer">
                            <tr style="background-color: #E4E7EC;">
                               <th><p style="margin: 0; font-size: 16px; color: #0C141D;">School Admin Name</p></th>
                                <th><p style="margin: 0; font-size: 16px; color: #0C141D;">School Admin Email</p></th>
                                <th><p style="margin: 0; font-size: 16px; color: #0C141D;">School Admin Password</p></th>
                            </tr>
                            <tr>
                                <td style="text-align: center; padding:16px 0 ; border-bottom: 1px solid #E4E7EC;">
                                   
                                    <p style="margin: 0;  color: #0C141D; font-size: 15px;">{{$data['admin_name']}}</p>
                                </td>
                                <td style="text-align: center; padding: 16px 0 ; border-bottom: 1px solid #E4E7EC;">
                                    <p style="margin: 0; font-size: 15px; color: #7B7F84;">{{$data['admin_email']}}</p>
                                </td>
                                <td style="text-align: center; padding: 16px  0; border-bottom: 1px solid #E4E7EC;">
                                    <p style="margin: 0; font-size: 15px; color: #7B7F84;">{{$data['admin_password']}}</p>
                                </td>
                            </tr>
                            
                        </table>
                    </td>
                </tr>
                <tr style="text-align: center;">
                    <td>
                        <span class="es-button-border msohide"><a href="{{url('/login')}}" class="es-button" target="_blank">Log in to your account</a></span>
                        <p style="font-size: 15px; color: #7B7F84; margin: 0; margin-top: 6px;"></p>
                        
                    </td>
                   
                </tr>
                <tr style="text-align: center;">
                    <td>
                        <p style="color: #7B7F84; font-size: 15px; line-height: 28px;">{{ get_settings('email_details') }}</p>
                    </td>
                </tr>

                <tr>
                    <td style="text-align:center; padding-bottom: 10px;"> 
                        <p style="text-align: center; font-size: 12px; color: #8D9197; font-weight: 400; margin-bottom: 30px;">{{ get_settings('warning_text') }} </p>                               
                        <a href="{{ get_settings('linkedin_link') }}" style="width:50px; height:50px; text-decoration: none; margin-right:40px"><img alt="Logo" src="{{ asset('assets/uploads/email_logo/'.get_settings('socialLogo1')) }}" width="20px" height="20px"></a>    
                        <a href="{{ get_settings('instagram_link') }}" style=" text-decoration: none; margin-right:40px"><img alt="Logo" src="{{ asset('assets/uploads/email_logo/'.get_settings('socialLogo2')) }}" width="20px" height="20px"></a>   
                        <a href="{{ get_settings('twitter_link') }}" style=" text-decoration: none;"><img alt="Logo" src="{{ asset('assets/uploads/email_logo/'.get_settings('socialLogo3')) }}" width="20px" height="20px"></a>  
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center;">
                        <p style="color: #191919; font-size: 13px;">{{ get_settings('address')}}</p>
                        <p style="color: #8D9197; font-size: 12px;">© {{ date('Y') }} {{ get_phrase('All Rights Reservedz') }}</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>  