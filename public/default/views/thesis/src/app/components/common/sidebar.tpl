<div class="admin-sidebar am-offcanvas" id="admin-offcanvas">
    <div class="am-offcanvas-bar admin-offcanvas-bar">
        <ul class="am-list admin-sidebar-list">
            <li v-for="item in links">
                <a v-link="item.url"><span :class="item.iconClass"></span>&nbsp;{{ item.name }}</a>
            </li>
        </ul>
    </div>
</div>