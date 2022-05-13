<aside class="main-sidebar">
    <section class="sidebar">
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{Auth::user()->avatar}}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{Auth::user()->name}}</p>
                <!-- <a href="#"><i class="fa fa-circle text-success"></i> Online</a> -->
            </div>
        </div>

        {{-- <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
            <input type="text" name="q" class="form-control" placeholder="Search...">
            <span class="input-group-btn">
                    <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                    </button>
                </span>
            </div>
        </form> --}}

        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">MAIN NAVIGATION</li>

            <li class="{{(isset($activemenu['main']) && $activemenu['main'] == 'dashboard') ? 'active' : ''}}">
                <a href="{{route('dashboard.home')}}"><i class="fa fa-dashboard"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            @if(Myhelper::hasrole(['superadmin','admin']))
                @if(Myhelper::can(['view_admins', 'view_banks', 'view_customers']))
                    <li class="treeview {{(isset($activemenu['main']) && $activemenu['main'] == 'members') ? 'active menu-open' : ''}}">
                        <a href="javascript:void(0);">
                            <i class="fa fa-user-circle"></i> <span>User Management</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if(Myhelper::can('view_customers'))
                                <li class="{{(isset($activemenu['sub']) && $activemenu['sub'] == 'customer') ? 'active' : ''}}"><a href="{{route('dashboard.members.index', ['type' => 'customer'])}}"><i class="fa fa-circle-o"></i> Customers</a></li>
                            @endif

                            <!-- @if(Myhelper::can('view_banks'))
                                <li class="{{(isset($activemenu['sub']) && $activemenu['sub'] == 'bank') ? 'active' : ''}}"><a href="{{route('dashboard.members.index', ['type' => 'bank'])}}"><i class="fa fa-circle-o"></i> Banks</a></li>
                            @endif -->

                            @if(Myhelper::can('view_admins'))
                                <li class="{{(isset($activemenu['sub']) && $activemenu['sub'] == 'admin') ? 'active' : ''}}"><a href="{{route('dashboard.members.index', ['type' => 'admin'])}}"><i class="fa fa-circle-o"></i> Admins</a></li>
                            @endif

                            @if(Myhelper::can('view_sellers'))
                                <li class="{{(isset($activemenu['sub']) && $activemenu['sub'] == 'seller') ? 'active' : ''}}"><a href="{{route('dashboard.members.index', ['type' => 'seller'])}}"><i class="fa fa-circle-o"></i> Sellers</a></li>
                            @endif
                        </ul>
                    </li>
                @endif

                @if(Myhelper::can(['category','brand','product']))
                    <li class="treeview {{(isset($activemenu['main']) && $activemenu['main'] == 'products') ? 'active menu-open' : ''}}">
                        <a href="javascript:void(0);">
                           <i class="fa fa-cog" aria-hidden="true"></i> <span>Product Management</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if(Myhelper::can('category'))
                                <li class="{{(isset($activemenu['sub']) && $activemenu['sub'] == 'category') ? 'active' : ''}}"><a href="{{route('dashboard.category.index', ['type' => 'category'])}}"><i class="fa fa-circle-o"></i> Category</a></li>
                            @endif
                            @if(Myhelper::can('brand'))
                                <li class="{{(isset($activemenu['sub']) && $activemenu['sub'] == 'brand') ? 'active' : ''}}"><a href="{{route('dashboard.brand.index', ['type' => 'brand'])}}"><i class="fa fa-circle-o"></i> Brand</a></li>
                            @endif
                             @if(Myhelper::can('product'))
                                <li class="{{(isset($activemenu['sub']) && $activemenu['sub'] == 'product') ? 'active' : ''}}"><a href="{{route('dashboard.product.index', ['type' => 'product'])}}"><i class="fa fa-circle-o"></i> Product</a></li>
                            @endif
                            <!-- @if(Myhelper::can('attributes'))
                                <li class="{{(isset($activemenu['sub']) && $activemenu['sub'] == 'attributes') ? 'active' : ''}}"><a href="{{route('dashboard.attributes.index', ['type' => 'attributes'])}}"><i class="fa fa-circle-o"></i> Attributes</a></li>
                            @endif -->
                        </ul>
                    </li>
                @endif
                @if(Myhelper::hasrole('superadmin'))
                    <li class="treeview {{(isset($activemenu['main']) && $activemenu['main'] == 'orders') ? 'active menu-open' : ''}}"> 
                        <a href="javascript:void(0);">
                            <i class="fa fa-gear"></i> <span>Orders</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="{{(isset($activemenu['sub']) && $activemenu['sub'] == 'orders') ? 'active' : ''}}"><a href="{{route('dashboard.orders.index', ['type' => 'orders'])}}"><i class="fa fa-circle-o"></i> Orders</a></li>
                        </ul>
                    </li>
                @endif
                @if(Myhelper::hasrole('superadmin'))
                    <li class="treeview {{(isset($activemenu['main']) && $activemenu['main'] == 'services') ? 'active menu-open' : ''}}"> 
                        <a href="javascript:void(0);">
                            <i class="fa fa-gear"></i> <span>Services</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="{{(isset($activemenu['sub']) && $activemenu['sub'] == 'services') ? 'active' : ''}}"><a href="{{route('dashboard.service.index')}}"><i class="fa fa-circle-o"></i> Services</a></li>
                            <li class="{{(isset($activemenu['sub']) && $activemenu['sub'] == 'request-service') ? 'active' : ''}}"><a href="{{route('dashboard.request_service.index')}}"><i class="fa fa-circle-o"></i> Requested Service</a></li>
                        </ul>
                    </li>
                @endif

                @if(Myhelper::hasrole('superadmin'))
                <li class="{{(isset($activemenu['main']) && $activemenu['main'] == 'department') ? 'active' : ''}}">
                    <a href="{{route('dashboard.department.index')}}"><i class="fa fa-dashboard"></i>
                        <span>Departments</span>
                    </a>
                </li>
                @endif
             <!--    @if(Myhelper::can(['view_bank_membership_packages','view_agent_membership_packages']))
                    <li class="treeview {{(isset($activemenu['main']) && $activemenu['main'] == 'resources') ? 'active menu-open' : ''}}">
                        <a href="javascript:void(0);">
                            <i class="fa fa-globe"></i> <span>Resources</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if(Myhelper::can(['view_bank_membership_packages','view_agent_membership_packages']))
                                <li class="treeview {{(isset($activemenu['sub']) && $activemenu['sub'] == 'packages') ? 'active' : ''}}">
                                    <a href="#"><i class="fa fa-circle-o"></i> Membership Packages
                                        <span class="pull-right-container">
                                            <i class="fa fa-angle-left pull-right"></i>
                                        </span>
                                    </a>
                                    <ul class="treeview-menu">
                                        @if(Myhelper::can('view_agent_membership_packages'))
                                            <li class="{{(isset($activemenu['child']) && $activemenu['child'] == 'agent') ? 'active' : ''}}"><a href="{{route('dashboard.resources.packages', ['type' => 'agent'])}}"><i class="fa fa-circle-o"></i> Agents</a></li>
                                        @endif

                                        @if(Myhelper::can('view_bank_membership_packages'))
                                            <li class="{{(isset($activemenu['child']) && $activemenu['child'] == 'bank') ? 'active' : ''}}"><a href="{{route('dashboard.resources.packages', ['type' => 'bank'])}}"><i class="fa fa-circle-o"></i> Banks</a></li>
                                        @endif
                                    </ul>
                                </li>
                            @endif

                            @if(Myhelper::can(['view_agent_schemes']))
                                <li class="{{(isset($activemenu['sub']) && $activemenu['sub'] == 'schemes') ? 'active' : ''}}"><a href="{{route('dashboard.resources.schemes', ['type' => 'agent'])}}"><i class="fa fa-circle-o"></i> Agent Schemes</a></li>
                            @endif
                        </ul>
                    </li>
                @endif  -->

                 @if(Myhelper::can(['view_faqs', 'view_contents', 'view_testimonials']))
                    <li class="treeview {{(isset($activemenu['main']) && $activemenu['main'] == 'cms') ? 'active menu-open' : ''}}">
                        <a href="javascript:void(0);">
                            <i class="fa fa-gears"></i> <span>Content Management</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if(Myhelper::can('view_faqs'))
                                <li class="{{(isset($activemenu['sub']) && $activemenu['sub'] == 'faqs') ? 'active' : ''}}"><a href="{{route('dashboard.cms.index', ['type' => 'faqs'])}}"><i class="fa fa-circle-o"></i> FAQs</a></li>
                            @endif

                            @if(Myhelper::can('view_contents'))
                                <li class="{{(isset($activemenu['sub']) && $activemenu['sub'] == 'contents') ? 'active' : ''}}"><a href="{{route('dashboard.cms.index', ['type' => 'contents'])}}"><i class="fa fa-circle-o"></i> CMS</a></li>
                            @endif

                            @if(Myhelper::can('view_testimonials'))
                                <li class="{{(isset($activemenu['sub']) && $activemenu['sub'] == 'testimonials') ? 'active' : ''}}"><a href="{{route('dashboard.cms.index', ['type' => 'testimonials'])}}"><i class="fa fa-circle-o"></i> Testimonials</a></li>
                            @endif
                        </ul>
                    </li>
                @endif 

                 @if(Myhelper::can(['view_blogs', 'add_blog']))
                    <li class="treeview {{(isset($activemenu['main']) && $activemenu['main'] == 'blogs') ? 'active menu-open' : ''}}">
                        <a href="javascript:void(0);">
                            <i class="fa fa-newspaper-o"></i> <span>Blogs Management</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if(Myhelper::can('add_blog'))
                                <li class="{{(isset($activemenu['sub']) && $activemenu['sub'] == 'add') ? 'active' : ''}}"><a href="{{route('dashboard.blogs.add')}}"><i class="fa fa-circle-o"></i> Create New</a></li>
                            @endif

                            @if(Myhelper::can('view_blogs'))
                                <li class="{{(isset($activemenu['sub']) && $activemenu['sub'] == 'index') ? 'active' : ''}}"><a href="{{route('dashboard.blogs.index')}}"><i class="fa fa-circle-o"></i> View All</a></li>
                            @endif
                        </ul>
                    </li>
                @endif 

              <!--    @if(Myhelper::can(['account_notification', 'sms_notification', 'push_notification', 'email_notification']))
                    <li class="treeview {{(isset($activemenu['main']) && $activemenu['main'] == 'notifications') ? 'active menu-open' : ''}}">
                        <a href="javascript:void(0);">
                            <i class="fa fa-bell"></i> <span>Notifications</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if(Myhelper::can('account_notification'))
                                <li class="{{(isset($activemenu['sub']) && $activemenu['sub'] == 'account') ? 'active' : ''}}"><a href="{{route('dashboard.notifications.index', ['type' => 'account'])}}"><i class="fa fa-circle-o"></i> Account Notification</a></li>
                            @endif

                            @if(Myhelper::can('sms_notification'))
                                <li class="{{(isset($activemenu['sub']) && $activemenu['sub'] == 'sms') ? 'active' : ''}}"><a href="{{route('dashboard.notifications.index', ['type' => 'sms'])}}"><i class="fa fa-circle-o"></i> SMS Notification</a></li>
                            @endif

                            @if(Myhelper::can('push_notification'))
                                <li class="{{(isset($activemenu['sub']) && $activemenu['sub'] == 'push') ? 'active' : ''}}"><a href="{{route('dashboard.notifications.index', ['type' => 'push'])}}"><i class="fa fa-circle-o"></i> Push Notification</a></li>
                            @endif

                            @if(Myhelper::can('email_notification'))
                                <li class="{{(isset($activemenu['sub']) && $activemenu['sub'] == 'email') ? 'active' : ''}}"><a href="{{route('dashboard.notifications.index', ['type' => 'email'])}}"><i class="fa fa-circle-o"></i> Email Notification</a></li>
                            @endif
                        </ul>
                    </li>
                @endif  -->

              <!--    @if(Myhelper::can(['view_loantypes']))
                    <li class="treeview {{(isset($activemenu['main']) && $activemenu['main'] == 'setup') ? 'active menu-open' : ''}}">
                        <a href="javascript:void(0);">
                            <i class="fa fa-gears"></i> <span>Setup</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="{{(isset($activemenu['sub']) && $activemenu['sub'] == 'loantypes') ? 'active' : ''}}"><a href="{{route('dashboard.setup.index', ['type' => 'loantypes'])}}"><i class="fa fa-circle-o"></i> Loan Types</a></li>
                        </ul>
                    </li>
                @endif  -->

                @if(Myhelper::hasrole('superadmin'))
                    <li class="treeview {{(isset($activemenu['main']) && $activemenu['main'] == 'tools') ? 'active menu-open' : ''}}">
                        <a href="javascript:void(0);">
                            <i class="fa fa-gear"></i> <span>Roles & Permission</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="{{(isset($activemenu['sub']) && $activemenu['sub'] == 'roles') ? 'active' : ''}}"><a href="{{route('dashboard.tools.roles')}}"><i class="fa fa-circle-o"></i> Roles</a></li>
                            <li class="{{(isset($activemenu['sub']) && $activemenu['sub'] == 'permissions') ? 'active' : ''}}"><a href="{{route('dashboard.tools.permissions')}}"><i class="fa fa-circle-o"></i> Permissions</a></li>
                        </ul>
                    </li>
                @endif

                @if(Myhelper::hasrole('superadmin'))
                    <li class="treeview {{(isset($activemenu['main']) && $activemenu['main'] == 'settings') ? 'active menu-open' : ''}}"> 
                        <a href="javascript:void(0);">
                            <i class="fa fa-gear"></i> <span>Site Settings</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="{{(isset($activemenu['sub']) && $activemenu['sub'] == 'settings') ? 'active' : ''}}"><a href="{{route('dashboard.settings.index')}}"><i class="fa fa-circle-o"></i> Settings</a></li>
                            @if(Myhelper::can('view_banner'))
                            <li class="{{(isset($activemenu['sub']) && $activemenu['sub'] == 'banners') ? 'active' : ''}}"><a href="{{route('dashboard.banner.index')}}"><i class="fa fa-circle-o"></i> Banners</a></li>
                            @endif
                        </ul>
                    </li>
                @endif

               
            @endif
        </ul>
    </section>
</aside>
