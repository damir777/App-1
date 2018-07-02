<div class="modal inmodal" id="clientPrice" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceIn">
            <div class="modal-header">
                <h4 class="modal-title">{{ trans('main.client_price') }}</h4>
            </div>
            <div class="modal-body">
                {{ Form::open(array('url' => '#', 'autocomplete' => 'off')) }}
                {{ Form::hidden('product', null, array('class' => 'product-id')) }}
                <label style="display: none" class="product-name-text"></label>
                <div class="row">
                    <div class="col-sm-6 col-sm-offset-3 text-center">
                        <div class="form-group">
                            <label>{{ trans('main.price') }}</label>
                            {{ Form::text('price', null, array('class' => 'form-control client-price')) }}
                        </div>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">{{ trans('main.cancel') }}</button>
                <button type="button" class="btn btn-primary insert-client-price">{{ trans('main.save') }}</button>
            </div>
        </div>
    </div>
</div>