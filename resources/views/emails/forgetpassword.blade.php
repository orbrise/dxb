<div class="inbox-data-content-intro" style="background-color: #0D1011; padding: 20px;">
    <div style="padding: 20px; max-width: 670px; margin: 0 auto; background-color: #0D1011; font: 14px / 1.5 &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; color: rgb(255, 255, 255);">
      <div style="border-bottom: 2px solid #2a2a2a; padding-bottom: 5px; margin-bottom: 20px;">
        <a href="{{ url('/') }}" title="evoory" style="color: #C1F11D; text-decoration: none; outline: 0px;">
          <img alt="evoory" src="https://assets.evoory.com/uploads/1776856883_Logo.png" width="180">
        </a>
      </div>
      <h1 style="font-size: 1.5em; margin-bottom: 1em; font-weight: 700; color: rgb(255, 255, 255);">Hi {{ $mailData['name'] }},</h1>
      <p style="font: 14px / 1.5 &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; color: rgb(255, 255, 255);"> Click here to set a new password: </p>
      <table cellpadding="0" cellspacing="0" style="display: inline-block; border-collapse: collapse; border-radius: 21.5px; font-family: Arial, Helvetica, sans-serif; font-size: 14px; text-align: center; text-decoration: none; cursor: pointer; line-height: 20px; background-color: #C1F11D; color: rgb(0, 0, 0);">
        <tbody>
          <tr>
            <td>
              <a href="{{url('change-password/'.$mailData['email'].'/'.$mailData['random'])}}" style="outline: 0px; color: rgb(0, 0, 0); text-decoration: none; font-weight: 600; line-height: 20px; padding: 8px 22px; display: block; width: auto;">Change my password</a>
            </td>
          </tr>
        </tbody>
      </table>
      <p style="font: 14px / 1.5 &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; color: rgb(255, 255, 255);">Button not working? Copy &amp; paste the link below in your browser:</p>
      <p style="font-size: 13px; color: #C1F11D; background: #131616; border: 1px solid #2a2a2a; padding: 10px 12px; border-radius: 5px; word-break: break-all; margin: 10px 0;">{{url('change-password/'.$mailData['email'].'/'.$mailData['random'])}}</p>
      <p style="font: 14px / 1.5 &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; color: rgb(255, 255, 255);">Thank You</p>
      <p style="font: 10pt / 1.5 &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; color: rgb(190, 190, 190); border-top: 2px solid #2a2a2a; margin-top: 20px; text-align: justify; padding-top: 5px;"> You have received this email because password reset was requested for <strong>
          <a href="mailto:{{$mailData['email']}}" style="color: #C1F11D; text-decoration: none; outline: 0px;">{{$mailData['email']}}</a>
        </strong>. If you have not requested a password reset on evoory please ignore this email - it is possible someone else has entered your email address by accident. Your password won't change unless you click the link above. </p>
      <div style="text-align: center; padding-top: 5px; margin-top: 5px; border-top: 2px solid #2a2a2a; font-size: 9pt;">
        <a href="{{ url('/') }}" style="color: #C1F11D; outline: 0px; text-decoration: underline;">Go to evoory.com</a>
      </div>
    </div>
  </div>
