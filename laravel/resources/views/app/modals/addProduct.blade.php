<div class="modal inmodal" id="addProduct" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceIn" id="tour-nine-step">
            <div class="modal-header">
                <h4 class="modal-title">{{ trans('main.new_product') }}</h4>
            </div>
            <div class="modal-body">
                {{ Form::open(array('url' => '#', 'autocomplete' => 'off', 'class' => 'product-form')) }}
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>{{ trans('main.category') }}</label>
                            <p class="label-add-btn add-category" data-toggle="modal" data-target="#addCategory">
                                {{ trans('main.add_new') }} <i class="fa fa-plus-circle" aria-hidden="true"></i>
                            </p>
                            {{ Form::select('category', $categories, null, array('class' => 'form-control category')) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>{{ trans('main.price') }} <i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip"
                                data-placement="top" title="" data-original-title="{{ trans('main.tooltip_price') }}"></i></label>
                            {{ Form::text('price', null, array('class' => 'form-control price')) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>{{ trans('main.code') }}</label>
                            {{ Form::text('code', null, array('class' => 'form-control code')) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>{{ trans('main.tax_group') }}</label>
                            {{ Form::select('tax_group', $tax_groups, null, array('class' => 'form-control tax-group')) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>{{ trans('main.name') }}</label>
                            {{ Form::text('name', null, array('class' => 'form-control form-product-name')) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>{{ trans('main.unit') }}</label>
                            {{ Form::select('unit', $units, null, array('class' => 'form-control unit')) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>{{ trans('main.type') }}</label>
                            {{ Form::select('service', array('T' => trans('main.service'),
                                'F' => trans('main.merchandise')), null, array('class' => 'form-control service')) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>{{ trans('main.description') }}</label>
                            {{ Form::text('description', null, array('class' => 'form-control description')) }}
                        </div>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">{{ trans('main.cancel') }}</button>
                <button type="button" class="btn btn-primary insert-product">{{ trans('main.save') }}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal inmodal" id="addCategory" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content animated bounceIn">
            <div class="modal-header">
                <h4 class="modal-title">{{ trans('main.new_category') }}</h4>
            </div>
            <div class="modal-body">
                {{ Form::open(array('url' => '#', 'autocomplete' => 'off')) }}
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>{{ trans('main.name') }}</label>
                            {{ Form::text('category', null, array('class' => 'form-control category-name')) }}
                        </div>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">{{ trans('main.cancel') }}</button>
                <button type="button" class="btn btn-primary insert-category">{{ trans('main.save') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
    var product_insert = '{{ trans('main.product_insert') }}';
    var category_insert = '{{ trans('main.category_insert') }}';
</script>

{{ HTML::script('js/functions/products.js?v='.date('YmdHi')) }}