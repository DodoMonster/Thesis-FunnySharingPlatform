<div class="row">
	<div class="col-md-5">
		<div class="dataTables_info" id="table-1_info" role="status" aria-live="polite">总共 
			<em class="font-style-normal text-googleplus">{{ page.totalNum }}</em> 条
		</div>
	</div>
	<div class="col-md-7">
		<div class="dataTables-paginate text-xs-right">
				<ul class="pagination">
					<li class="paginate_button page-item previous"
						v-if="page.cur != 1">
						<a class="page-link" @click="page.cur--">
							<span aria-hidden="true">«</span>
						</a>
					</li>
					<li class="page-item" :class="{'active' : page.cur == index}"
						v-for="index in indexs"@click="btnClick(index)">
						<a class="page-link" href="javascript:;">{{index}}</a>
					</li>
					
					<li class="paginate_button page-item next" v-if="page.totalPage>1 && page.cur <= page.totalPage">
						<a class="page-link" @click="page.cur++" href="javascript:;">
							<span aria-hidden="true">»</span>			
						</a>
					</li>
				</ul>
		</div>
	</div>
</div>
