<div class="inbox-data-content-intro" style="background-color: #232323; padding: 20px;">
    <div style="padding: 20px; max-width: 670px; margin: 0 auto; background-color: #232323; font: 14px / 1.5 &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; color: rgb(255, 255, 255);">
      <div style="border-bottom: 2px solid rgb(51, 51, 51); padding-bottom: 5px; margin-bottom: 20px;">
        <a href="https://ae.massagerepublic.com.co" title="Massage Republic" style="color: rgb(244, 184, 39); text-decoration: none; outline: 0px;">
          <img alt="Massage Republic" src="https://assets.massagerepublic.com.co/assets/images/web/maillogo.gif" width="300">
        </a>
      </div>
      <h1 style="font-size: 1.5em; margin-bottom: 1em; font-weight: 700; color: rgb(255, 255, 255);">Hi {{ $mailData['name'] }},</h1>
      <p style="font: 14px / 1.5 &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; color: rgb(255, 255, 255);"> Click here to set a new password: </p>
      <table cellpadding="0" cellspacing="0" style="display: inline-block; border-collapse: collapse; border-radius: 3px; font-family: Arial, Helvetica, sans-serif; font-size: 20px; text-align: center; text-decoration: none; cursor: pointer; line-height: 20px; text-shadow: rgb(253, 232, 119) 0px 1px 0px; background-color: rgb(244, 184, 39); border: 1px solid rgb(0, 0, 0); color: rgb(0, 0, 0);">
        <tbody>
          <tr>
            <td>
              <a href="{{url('change-password/'.$mailData['email'].'/'.$mailData['random'])}}" style="outline: 0px; color: rgb(0, 0, 0); text-decoration: none; line-height: 20px; padding: 10px 20px; display: block; width: auto;">Change my password</a>
            </td>
          </tr>
        </tbody>
      </table>
      <p style="font: 14px / 1.5 &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; color: rgb(255, 255, 255);">Button not working? Copy &amp; paste the link below in your browser: <br>
        <span style="color: rgb(17, 85, 204); background: rgb(128, 128, 156); padding: 2px;">{{url('change-password/'.$mailData['email'].'/'.$mailData['random'])}}</span>
      </p>
      <p style="font: 14px / 1.5 &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; color: rgb(255, 255, 255);">Thank You</p>
      <p style="font: 10pt / 1.5 &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; color: rgb(190, 190, 190); border-top: 2px solid rgb(51, 51, 51); margin-top: 20px; text-align: justify; padding-top: 5px;"> You have received this email because password reset was requested for <strong>
          <a href="mailto:{{$mailData['email']}}" style="color: rgb(244, 184, 39); text-decoration: none; outline: 0px;">{{$mailData['email']}}</a>
        </strong>. If you have not requested a password reset on Massage Republic please ignore this email - it is possible someone else has entered your email address by accident. Your password won't change unless you click the link above. </p>
      <div style="text-align: center; padding-top: 5px; margin-top: 5px; border-top: 2px solid rgb(51, 51, 51); font-size: 9pt;">
        <a href="https://ae.massagerepublic.com.co" style="color: rgb(244, 184, 39); outline: 0px; text-decoration: underline;">Go to ae.MassageRepublic.com</a> - Site blocked? Try: <a target="_blank" href="http://escorts.ninja?utm_campaign=devise%2Fmailer-devise_mail&amp;utm_content=Reset%20password%20instructions&amp;utm_medium=email&amp;utm_source=AppMail" style="color: rgb(244, 184, 39); outline: 0px; text-decoration: underline;">Escorts.ninja</a> or <a target="_blank" href="http://ae.massagerepublic.com.co" style="color: rgb(244, 184, 39); outline: 0px; text-decoration: underline;">ae.MassageRepublic.com.cp</a>
      </div>
    </div>
  </div>
