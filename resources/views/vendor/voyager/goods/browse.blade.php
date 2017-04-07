@extends('voyager::master')
@section('page_title','All '.$dataType->display_name_plural)
@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i> {{ $dataType->display_name_plural }}
        @if (Voyager::can('add_'.$dataType->name))
            <a href="{{ route('voyager.'.$dataType->slug.'.create') }}" class="btn btn-success">
                <i class="voyager-plus"></i> 添加
            </a>
            <button type="button" class="btn btn-success" id="new_folder"
                    onclick="jQuery('#new_folder_modal').modal('show');"><i class="voyager-folder"></i>
                批量导入
            </button>
        @endif
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content container-fluid">
        @include('voyager::alerts')
        <div class="row" id="filemanager">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body table-responsive">
                        <table id="dataTable" class="row table table-hover">
                            <thead>
                            <tr>
                                @foreach($dataType->browseRows as $rows)
                                    <th>{{ $rows->display_name }}</th>
                                @endforeach
                                <th class="actions">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($dataTypeContent as $data)
                                <tr>
                                    @foreach($dataType->browseRows as $row)
                                        <td>
											<?php $options = json_decode($row->details); ?>
                                            @if($row->type == 'image')
                                                <img src="@if( strpos($data->{$row->field}, 'http://') === false && strpos($data->{$row->field}, 'https://') === false){{ Voyager::image( $data->{$row->field} ) }}@else{{ $data->{$row->field} }}@endif"
                                                     style="width:100px">
                                            @elseif($row->type == 'select_multiple')
                                                @if(property_exists($options, 'relationship'))

                                                    @foreach($data->{$row->field} as $item)
                                                        @if($item->{$row->field . '_page_slug'})
                                                            <a href="{{ $item->{$row->field . '_page_slug'} }}">{{ $item->{$row->field} }}</a>@if(!$loop->last)
                                                                , @endif
                                                        @else
                                                            {{ $item->{$row->field} }}
                                                        @endif
                                                    @endforeach

                                                    {{-- $data->{$row->field}->implode($options->relationship->label, ', ') --}}
                                                @elseif(property_exists($options, 'options'))
                                                    @foreach($data->{$row->field} as $item)
                                                        {{ $options->options->{$item} . (!$loop->last ? ', ' : '') }}
                                                    @endforeach
                                                @endif
                                                @if ($data->{$row->field} && isset($options->relationship))
                                                    {{ $data->{$row->field}->implode($options->relationship->label, ', ') }}
                                                @endif
                                            @elseif($row->type == 'select_dropdown' && property_exists($options, 'options'))

                                                @if($data->{$row->field . '_page_slug'})
                                                    <a href="{{ $data->{$row->field . '_page_slug'} }}">{!! $options->options->{$data->{$row->field}} !!}</a>
                                                @else
                                                    {!! $options->options->{$data->{$row->field}} !!}
                                                @endif


                                            @elseif($row->type == 'select_dropdown' && $data->{$row->field . '_page_slug'})
                                                <a href="{{ $data->{$row->field . '_page_slug'} }}">{{ $data->{$row->field} }}</a>
                                            @elseif($row->type == 'date')
                                                {{ $options && property_exists($options, 'format') ? \Carbon\Carbon::parse($data->{$row->field})->formatLocalized($options->format) : $data->{$row->field} }}
                                            @elseif($row->type == 'checkbox')
                                                @if($options && property_exists($options, 'on') && property_exists($options, 'off'))
                                                    @if($data->{$row->field})
                                                        <span class="label label-info">{{ $options->on }}</span>
                                                    @else
                                                        <span class="label label-primary">{{ $options->off }}</span>
                                                    @endif
                                                @else
                                                    {{ $data->{$row->field} }}
                                                @endif
                                            @elseif($row->type == 'text')
                                                @include('voyager::multilingual.input-hidden-bread-browse')
                                                <div class="readmore">{{ strlen( $data->{$row->field} ) > 200 ? substr($data->{$row->field}, 0, 200) . ' ...' : $data->{$row->field} }}</div>
                                            @elseif($row->type == 'text_area')
                                                @include('voyager::multilingual.input-hidden-bread-browse')
                                                <div class="readmore">{{ strlen( $data->{$row->field} ) > 200 ? substr($data->{$row->field}, 0, 200) . ' ...' : $data->{$row->field} }}</div>
                                            @elseif($row->type == 'rich_text_box')
                                                @include('voyager::multilingual.input-hidden-bread-browse')
                                                <div class="readmore">{{ strlen( strip_tags($data->{$row->field}, '<b><i><u>') ) > 200 ? substr(strip_tags($data->{$row->field}, '<b><i><u>'), 0, 200) . ' ...' : strip_tags($data->{$row->field}, '<b><i><u>') }}</div>
                                            @else
                                                @include('voyager::multilingual.input-hidden-bread-browse')
                                                <span>{{ $data->{$row->field} }}</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="no-sort no-click" id="bread-actions">
                                        @if (Voyager::can('delete_'.$dataType->name))
                                            <a href="javascript:;" title="Delete"
                                               class="btn btn-sm btn-danger pull-right delete" data-id="{{ $data->id }}"
                                               id="delete-{{ $data->id }}">
                                                <i class="voyager-trash"></i> <span
                                                        class="hidden-xs hidden-sm">删除</span>
                                            </a>
                                        @endif
                                        @if (Voyager::can('edit_'.$dataType->name))
                                            <a href="{{ route('voyager.'.$dataType->slug.'.edit', $data->id) }}"
                                               title="Edit" class="btn btn-sm btn-primary pull-right edit">
                                                <i class="voyager-edit"></i> <span class="hidden-xs hidden-sm">修改</span>
                                            </a>
                                        @endif
                                        @if (Voyager::can('read_'.$dataType->name))
                                            <a href="{{ route('voyager.'.$dataType->slug.'.show', $data->id) }}"
                                               title="View" class="btn btn-sm btn-warning pull-right">
                                                <i class="voyager-eye"></i> <span class="hidden-xs hidden-sm">查看</span>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @if (isset($dataType->server_side) && $dataType->server_side)
                            <div class="pull-left">
                                <div role="status" class="show-res" aria-live="polite">
                                    Showing {{ $dataTypeContent->firstItem() }} to {{ $dataTypeContent->lastItem() }}
                                    of {{ $dataTypeContent->total() }} entries
                                </div>
                            </div>
                            <div class="pull-right">
                                {{ $dataTypeContent->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-trash"></i>
                        你确认要删除 {{ strtolower($dataType->display_name_singular) }}?</h4>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('voyager.'.$dataType->slug.'.index') }}" id="delete_form" method="POST">
                        {{ method_field("DELETE") }}
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-danger pull-right delete-confirm"
                               value="确认 {{ strtolower($dataType->display_name_singular) }}">
                    </form>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">取消</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <!-- New Folder Modal -->
    <div class="modal fade modal-info" id="new_folder_modal">
        <form action="{{ route('import') }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"
                                aria-hidden="true">&times;
                        </button>
                        <h4 class="modal-title"><i class="voyager-folder"></i> 批量导入</h4>
                    </div>

                    <div class="modal-body">
                        @php
                            $typeList = \App\Model\Admin\GoodsType::where('status', 1)->orderBy('id', 'desc')->get();
                        @endphp
                        商品分类：<select class="form-control" name="type_id" id="goods_type">
                            @foreach($typeList as $type)
                                <option value="{{ $type->id }}">{{ $type->type_name }}</option>
                            @endforeach
                        </select>
                        Excel文件：
                        <input type="file" name="import_goods">

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        {{--<button type="button" class="btn btn-info" id="import_goods">保存</button>--}}
                        <input type="submit" class="btn btn-info" id="import_goods" value="提交">
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- End New Folder Modal -->
@stop

@section('css')
    @if(!$dataType->server_side && config('dashboard.data_tables.responsive'))
        <link rel="stylesheet" href="{{ voyager_asset('lib/css/responsive.dataTables.min.css') }}">
    @endif
    <script type="text/javascript" src="{{ voyager_asset('js/vue1.min.js') }}"></script>
@stop


@section('javascript')
    <!-- DataTables -->
    @if(!$dataType->server_side && config('dashboard.data_tables.responsive'))
        <script src="{{ voyager_asset('lib/js/dataTables.responsive.min.js') }}"></script>
    @endif
    @if($isModelTranslatable)
        <script src="{{ voyager_asset('js/multilingual.js') }}"></script>
    @endif
    <script>

        $(document).ready(function () {
                    @if (!$dataType->server_side)
            var table = $('#dataTable').DataTable({
                    "order": []
                        @if(config('dashboard.data_tables.responsive')), responsive: true @endif
                });
            @endif

            @if ($isModelTranslatable)
                $('.side-body').multilingual();
            @endif

            /*$('#import_goods').click(function(){
             CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
             var manager = new Vue({
             el: '#filemanager',
             data: {
             files: '',
             folders: [],
             selected_file: '',
             directories: [],
             },
             });
             source = manager.selected_file.path;
             filename = manager.selected_file.name;
             var goodsType = $('#goods_type').val();
             console.log(manager)
             console.log(filename)
             console.log(goodsType)

             $('#import_goods').modal('hide');
             $.post('{ {  route('voyager.dashboard') }}/goods/import', { filename: filename, goodsType: goodsType, _token: CSRF_TOKEN }, function(data){
             if(data.success == true){
             toastr.success('Successfully renamed file/folder', "Sweet Success!");
             getFiles(manager.folders);
             } else {
             toastr.error(data.error, "Whoops!");
             }
             });
             });
             });*/


            var deleteFormAction;
            $('td').on('click', '.delete', function (e) {
                var form = $('#delete_form')[0];

                if (!deleteFormAction) { // Save form action initial value
                    deleteFormAction = form.action;
                }

                form.action = deleteFormAction.match(/\/[0-9]+$/)
                    ? deleteFormAction.replace(/([0-9]+$)/, $(this).data('id'))
                    : deleteFormAction + '/' + $(this).data('id');
                console.log(form.action);

                $('#delete_modal').modal('show');
            });

        //        $('#import_goods').click(function(){
        //            if(typeof(manager.selected_file) !== 'undefined'){
        //                $('#import_goods').val(manager.selected_file.name);
        //            }
        //            $('#new_folder_modal').modal('show');
        //        });
        //

    </script>
@stop