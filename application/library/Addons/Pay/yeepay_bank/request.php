<?php
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<title>1、订单支付接口演示</title>
</head>
	<body>
		<table width="80%" border="0" align="center" cellpadding="5" cellspacing="0" style="border:solid 1px #107929">
			<tr>
		  		<th align="center" height="20" colspan="5" bgcolor="#6BBE18">
					请输入订单支付参数	
				</th>
		  	</tr> 

			<form method="post" action="sendRequest.php" target="_blank">
				<tr >
					<td width="20%" align="left">&nbsp;商户订单号</td>
					<td width="5%"  align="center"> : &nbsp;</td> 
					<td width="55%" align="left"> 
						<input size="70" type="text" name="orderid" />
						<span style="color:#FF0000;font-weight:100;">*</span>
					</td>
					<td width="5%"  align="center"> - </td> 
					<td width="15%" align="left">orderid</td> 
				</tr>

				<tr >
					<td width="20%" align="left">&nbsp;交易发生时间</td>
					<td width="5%"  align="center"> : &nbsp;</td> 
					<td width="55%" align="left"> 
						<input size="70" type="text" name="transtime" value="<?php echo time();?>" />
						<span style="color:#FF0000;font-weight:100;">*</span>
					</td>
					<td width="5%"  align="center"> - </td> 
					<td width="15%" align="left">transtime</td> 
				</tr>
				
				<tr >
					<td width="20%" align="left">&nbsp;交易金额</td>
					<td width="5%"  align="center"> : &nbsp;</td> 
					<td width="55%" align="left"> 
						<input size="70" type="text" name="amount" value="2" />
						<span style="color:#FF0000;font-weight:100;">*</span>
					</td>
					<td width="5%"  align="center"> - </td> 
					<td width="15%" align="left">amount</td> 
				</tr>
								
				<tr >
					<td width="20%" align="left">&nbsp;交易币种</td>
					<td width="5%"  align="center"> : &nbsp;</td> 
					<td width="55%" align="left"> 
						<input size="70" type="text" readonly="readonly" name="currency" value="156" />
					</td>
					<td width="5%"  align="center"> - </td> 
					<td width="15%" align="left">currency</td> 
				</tr>
				
				<tr >
					<td width="20%" align="left">&nbsp;商品类别码</td>
					<td width="5%"  align="center"> : &nbsp;</td> 
					<td width="55%" align="left"> 
						<input size="70" type="text" name="productcatalog" value="1" />
						<span style="color:#FF0000;font-weight:100;">*</span>
					</td>
					<td width="5%"  align="center"> - </td> 
					<td width="15%" align="left">productcatalog</td> 
				</tr>

				<tr >
					<td width="20%" align="left">&nbsp;商品名称</td>
					<td width="5%"  align="center"> : &nbsp;</td> 
					<td width="55%" align="left"> 
						<input size="70" type="text" name="productname" value="一键支付-测试" />
						<span style="color:#FF0000;font-weight:100;">*</span>
					</td>
					<td width="5%"  align="center"> - </td> 
					<td width="15%" align="left">productname</td> 
				</tr>
				
				<tr >
					<td width="20%" align="left">&nbsp;商品描述</td>
					<td width="5%"  align="center"> : &nbsp;</td> 
					<td width="55%" align="left"> 
						<input size="70" type="text" name="productdesc" value="productdesc" />
					</td>
					<td width="5%"  align="center"> - </td> 
					<td width="15%" align="left">productdesc</td> 
				</tr>
				
				<tr >
					<td width="20%" align="left">&nbsp;用户标识类型</td>
					<td width="5%"  align="center"> : &nbsp;</td> 
					<td width="55%" align="left"> 
						<input size="70" type="text" name="identitytype" value="2" />
						<span style="color:#FF0000;font-weight:100;">*</span>
					</td>
					<td width="5%"  align="center"> - </td> 
					<td width="15%" align="left">identitytype</td> 
				</tr>
				
				<tr >
					<td width="20%" align="left">&nbsp;用户标识</td>
					<td width="5%"  align="center"> : &nbsp;</td> 
					<td width="55%" align="left"> 
						<input size="70" type="text" name="identityid" value="" />
						<span style="color:#FF0000;font-weight:100;">*</span>
					</td>
					<td width="5%"  align="center"> - </td> 
					<td width="15%" align="left">identityid</td> 
				</tr>
				
				<tr >
					<td width="20%" align="left">&nbsp;终端标识类型</td>
					<td width="5%"  align="center"> : &nbsp;</td> 
					<td width="55%" align="left"> 
						<input size="70" type="text" name="terminaltype" value="1" />
						<span style="color:#FF0000;font-weight:100;">*</span>
					</td>
					<td width="5%"  align="center"> - </td> 
					<td width="15%" align="left">terminaltype</td> 
				</tr>
				
				<tr >
					<td width="20%" align="left">&nbsp;终端标识ID</td>
					<td width="5%"  align="center"> : &nbsp;</td> 
					<td width="55%" align="left"> 
						<input size="70" type="text" name="terminalid" value="44-45-53-54-00-00" />
						<span style="color:#FF0000;font-weight:100;">*</span>
					</td>
					<td width="5%"  align="center"> - </td> 
					<td width="15%" align="left">terminalid</td> 
				</tr>
				
				<tr >
					<td width="20%" align="left">&nbsp;用户IP地址</td>
					<td width="5%"  align="center"> : &nbsp;</td> 
					<td width="55%" align="left"> 
						<input size="70" type="text" name="userip" value="127.0.0.0" />
						<span style="color:#FF0000;font-weight:100;">*</span>
					</td>
					<td width="5%"  align="center"> - </td> 
					<td width="15%" align="left">userip</td> 
				</tr>
				
				<tr >
					<td width="20%" align="left">&nbsp;终端设备UA</td>
					<td width="5%"  align="center"> : &nbsp;</td> 
					<td width="55%" align="left"> 
						<input size="70" type="text" name="userua" value="Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36" />
						<span style="color:#FF0000;font-weight:100;">*</span>
					</td>
					<td width="5%"  align="center"> - </td> 
					<td width="15%" align="left">userua</td> 
				</tr>
				
				<tr >
					<td width="20%" align="left">&nbsp;页面回调地址</td>
					<td width="5%"  align="center"> : &nbsp;</td> 
					<td width="55%" align="left"> 
						<input size="70" type="text" name="fcallbackurl"
							   value="http://localhost:8080/InstantPay/callback.jsp" />
					</td>
					<td width="5%"  align="center"> - </td> 
					<td width="15%" align="left">fcallbackurl</td> 
				</tr>


				<tr >
					<td width="20%" align="left">&nbsp;后台回调地址</td>
					<td width="5%"  align="center"> : &nbsp;</td> 
					<td width="55%" align="left"> 
						<input size="70" type="text" name="callbackurl" 
							   value="" />
					</td>
					<td width="5%"  align="center"> - </td> 
					<td width="15%" align="left">callbackurl</td> 
				</tr> 
				
				<tr >
					<td width="20%" align="left">&nbsp;支付方式</td>
					<td width="5%"  align="center"> : &nbsp;</td> 
					<td width="55%" align="left"> 
						<input size="70" type="text" name="paytypes" value="" />
					</td>
					<td width="5%"  align="center"> - </td> 
					<td width="15%" align="left">paytypes</td> 
				</tr>
				
				<tr >
					<td width="20%" align="left">&nbsp;订单有效期</td>
					<td width="5%"  align="center"> : &nbsp;</td> 
					<td width="55%" align="left"> 
						<input size="70" type="text" name="orderexpdate" value="60" />
					</td>
					<td width="5%"  align="center"> - </td> 
					<td width="15%" align="left">orderexpdate</td> 
				</tr>

				<tr >
					<td width="20%" align="left">&nbsp;银行卡号</td>
					<td width="5%"  align="center"> : &nbsp;</td> 
					<td width="55%" align="left"> 
						<input size="70" type="text" name="cardno" value="" />
					</td>
					<td width="5%"  align="center"> - </td> 
					<td width="15%" align="left">cardno</td> 
				</tr>

				<tr >
					<td width="20%" align="left">&nbsp;证件类型</td>
					<td width="5%"  align="center"> : &nbsp;</td> 
					<td width="55%" align="left"> 
						<input size="70" type="text" name="idcardtype" value="" />
					</td>
					<td width="5%"  align="center"> - </td> 
					<td width="15%" align="left">idcardtype</td> 
				</tr>

				<tr >
					<td width="20%" align="left">&nbsp;证件号</td>
					<td width="5%"  align="center"> : &nbsp;</td> 
					<td width="55%" align="left"> 
						<input size="70" type="text" name="idcard" value="" />
					</td>
					<td width="5%"  align="center"> - </td> 
					<td width="15%" align="left">idcard</td> 
				</tr>

				<tr >
					<td width="20%" align="left">&nbsp;持卡人姓名</td>
					<td width="5%"  align="center"> : &nbsp;</td> 
					<td width="55%" align="left"> 
						<input size="70" type="text" name="owner" value="" />
					</td>
					<td width="5%"  align="center"> - </td> 
					<td width="15%" align="left">owner</td> 
				</tr>

				<tr >
					<td width="20%" align="left">&nbsp;</td>
					<td width="5%"  align="center">&nbsp;</td> 
					<td width="55%" align="left"> 
						<input type="submit" value="单击支付" />
					</td>
					<td width="5%"  align="center">&nbsp;</td> 
					<td width="15%" align="left">&nbsp;</td> 
				</tr>

			</form>
		</table>
</body>
</html>
