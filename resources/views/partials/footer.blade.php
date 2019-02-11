<div class="footer">
    <div class="container">
        <div class="row no-gutters">
            <div class="col-sm-6 col-lg-3 text-center px-5">
                <h5>SUBSCRIBE</h5>
                <form>
                    <div class="form-group">
                        <input type="email" autocomplete="email"
                               class="form-control rounded-pill text-center" placeholder="">
                    </div>
                    <button type="button" class="btn btn-primary btn-block rounded-pill">SUBSCRIBE</button>
                </form>
            </div>
            <div class="col-6 col-lg-3">
                <h6 class="bold">Service</h6>
                <div class="list-group list-group-flush list-group-no-border list-group-sm">
                    <a href="javascript:void(0)" class="list-group-item list-group-item-action">Help</a>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <h6 class="bold">{{env('APP_NAME')}}</h6>
                <div class="list-group list-group-flush list-group-no-border list-group-sm">
                    <a href="about.html" class="list-group-item list-group-item-action">About Us</a>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3"></div>
        </div>
    </div>
</div>
<div class="copyright">{{trans('general.copyright')}}, {{env('APP_NAME')}}</div>