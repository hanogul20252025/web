@extends('layouts.admin.app')

@section('title',translate('messages.Add_new_sub_category'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">
                        <div class="card-header-icon d-inline-flex mr-2 img">
                            <img src="{{dynamicAsset('public/assets/admin/img/sub-category.png')}}" alt="">
                        </div>
                        <span>{{translate('messages.Sub_Category_Setup')}}</span>
                    </h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="card resturant--cate-form">
            <div class="card-body">
                <form action="{{isset($category)?route('admin.category.update',[$category['id']]):route('admin.category.store')}}" method="post">
                @csrf
                @php($language=\App\Models\BusinessSetting::where('key','language')->first())
                @php($language = $language->value ?? null)
                @php($default_lang = str_replace('_', '-', app()->getLocale()))
                @if($language)
                                <div class="js-nav-scroller hs-nav-scroller-horizontal">
                    <ul class="nav nav-tabs mb-4">
                        <li class="nav-item">
                            <a class="nav-link lang_link active" href="#" id="default-link">{{translate('Default')}}</a>
                        </li>
                        @foreach(json_decode($language) as $lang)
                            <li class="nav-item">
                                <a class="nav-link lang_link" href="#" id="{{$lang}}-link">{{\App\CentralLogics\Helpers::get_language_name($lang).'('.strtoupper($lang).')'}}</a>
                            </li>
                        @endforeach
                    </ul>
                                </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-label"
                                    for="parent_id">{{translate('messages.main_category')}}
                                    <span class="input-label-secondary">*</span></label>
                                <select id="parent_id" name="parent_id" class="form-control js-select2-custom" required>
                                    <option value="" selected disabled>{{ translate('Select_Category') }}</option>
                                    @foreach(\App\Models\Category::where(['position'=>0])->get(['id','name']) as $cat)
                                        <option value="{{$cat['id']}}" {{isset($category)?($category['parent_id']==$cat['id']?'selected':''):''}} >{{$cat['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <input name="position" value="1" type="hidden">
                        </div>
                        <div class="col-md-6">

                            <div class="form-group lang_form" id="default-form">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.name')}} ({{translate('Default')}}) </label>
                                <input type="text" name="name[]" class="form-control" placeholder="{{ translate('Ex:_Sub_Category_Name') }}"   maxlength="191">
                            </div>

                            <input type="hidden" name="lang[]" value="default">

                        @foreach(json_decode($language) as $lang)
                                <div class="form-group d-none lang_form" id="{{$lang}}-form">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('messages.name')}} ({{strtoupper($lang)}})</label>
                                    <input type="text" name="name[]" class="form-control" placeholder="{{ translate('Ex:_Sub_Category_Name') }}" maxlength="191"  >
                                </div>
                                <input type="hidden" name="lang[]" value="{{$lang}}">
                        @endforeach
                    @else
                            <div class="form-group" id="default-form">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.name')}} {{translate('Default')}}</label>
                                <input type="text" name="name[]" class="form-control" placeholder="{{ translate('Ex:_Sub_Category_Name') }}"  maxlength="191">
                            </div>
                            <input type="hidden" name="lang[]" value="default">
                            @endif
                        </div>
                        <div class="col-md-12">
                            <div class="btn--container justify-content-end">
                                <!-- Static Button -->
                                <button type="reset" id="reset_btn" class="btn btn--reset">{{translate('reset')}}</button>
                                <!-- Static Button -->
                                <button type="submit" class="btn btn--primary">{{isset($category)?translate('messages.update'):translate('messages.submit')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card mt-2">
            <div class="card-header py-2 border-0">
                <div class="search--button-wrapper">
                    <h5 class="card-title">{{translate('messages.sub_category_list')}}<span class="badge badge-soft-dark ml-2" id="itemCount">{{$categories->total()}}</span></h5>
                    <form>
                        <!-- Search -->
                        <div class="input--group input-group input-group-merge input-group-flush">
                            <input id="datatableSearch" name="search" value="{{ request()?->search ?? null }}" type="search" class="form-control" placeholder="{{ translate('Ex_:_Sub_Categories') }}" aria-label="{{translate('messages.search_sub_categories')}}">
                            <input type="hidden" name="sub_category" value="1">
                            <button type="submit" class="btn btn--secondary">
                                <i class="tio-search"></i>
                            </button>
                        </div>
                        <!-- End Search -->
                    </form>
                </div>
            </div>
            <div class="card-body px-0 pt-0">
                <div class="table-responsive datatable-custom">
                    <table id="columnSearchDatatable"
                        class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                        data-hs-datatables-options='{
                            "search": "#datatableSearch",
                            "entries": "#datatableEntries",
                            "isResponsive": false,
                            "isShowPaging": false,
                            "paging":false,
                        }'>
                        <thead class="thead-light">
                            <tr>
                                <th>{{ translate('messages.sl') }}</th>
                                <th>{{translate('messages.sub_category')}}</th>
                                <th>{{translate('messages.id')}}</th>
                                <th>{{translate('messages.main_category')}}</th>
                                <th><div class="ml-3"> {{translate('messages.priority')}}</div></th>
                                <th class="w-100px">{{translate('messages.status')}}</th>
                                <th class="text-center">{{translate('messages.action')}}</th>
                            </tr>
                        </thead>

                        <tbody id="table-div">
                        @foreach($categories as $key=>$category)
                            <tr>
                                <td>{{$key+$categories->firstItem()}}</td>
                                <td>
                                    <span class="d-block font-size-sm text-body">
                                        {{Str::limit($category->name,20,'...')}}
                                    </span>
                                </td>
                                <td>{{$category->id}}</td>
                                <td>
                                    <span class="d-block font-size-sm text-body">
                                        {{Str::limit($category?->parent?->name,20,'...')}}
                                    </span>
                                </td>
                                <td>
                                    <form action="{{route('admin.category.priority',$category->id)}}" class="priority-form">
                                    <select name="priority" id="priority" class="form-control form--control-select priority-select {{$category->priority == 0 ? 'text--title border-dark':''}} {{$category->priority == 1 ? 'text--info border-info':''}} {{$category->priority == 2 ? 'text--success border-success':''}} ">
                                        <option value="0" {{$category->priority == 0?'selected':''}}>{{translate('messages.normal')}}</option>
                                        <option value="1" {{$category->priority == 1?'selected':''}}>{{translate('messages.medium')}}</option>
                                        <option value="2" {{$category->priority == 2?'selected':''}}>{{translate('messages.high')}}</option>
                                    </select>
                                    </form>
                                </td>
                                <td>
                                    <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$category->id}}">
                                    <input type="checkbox" data-url="{{route('admin.category.status',[$category['id'],$category->status?0:1])}}" class="toggle-switch-input redirect-url" id="stocksCheckbox{{$category->id}}" {{$category->status?'checked':''}}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="btn btn-sm btn--primary btn-outline-primary action-btn"
                                            href="{{route('admin.category.edit',[$category['id']])}}" title="{{translate('messages.edit_category')}}"><i class="tio-edit"></i>
                                        </a>
                                        <a class="btn btn-sm btn--danger btn-outline-danger action-btn form-alert" href="javascript:"
                                        data-id="category-{{$category['id']}}" data-message="{{ translate('Want_to_delete_this_sub_category_?') }}" title="{{translate('messages.delete_sub_category')}}"><i class="tio-delete-outlined"></i>
                                        </a>
                                    </div>
                                    <form action="{{route('admin.category.delete',[$category['id']])}}" method="post" id="category-{{$category['id']}}">
                                        @csrf @method('delete')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @if(count($categories) === 0)
                    <div class="empty--data">
                        <img src="{{dynamicAsset('/public/assets/admin/img/empty.png')}}" alt="public">
                        <h5>
                            {{translate('no_data_found')}}
                        </h5>
                    </div>
                    @endif
                    <div class="page-area px-4 pt-3 pb-0">
                        <div class="d-flex align-items-center justify-content-end">
                            <div>
                    {!! $categories->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script src="{{dynamicAsset('public/assets/admin')}}/js/view-pages/sub-category-index.js"></script>
@endpush
