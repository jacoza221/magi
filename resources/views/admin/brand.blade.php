@extends('layouts.app')
@section('container')
<div class="col-lg-12">
    <div class="row">
        <div class="col-lg-3">
        @include('components.mypage.menu')
        </div>
        <div class="col-lg-9">
            <div class="contanier">
                <div class="card">
                    <div class="card-header">
                        <h3>ブランド</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <button type="button" class="mx-3 col-4 btn btn-outline-primary block float-start float-lg-end m-2" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                            ブランドを追加    
                            </button>
                        </div>
                        <div class="card-content">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>ブランド</th>
                                            <th>カテゴリー</th>
                                            <th>IMG</th>
                                            <th>編集</th>
                                            <th>削除</th>
                                        </tr>
                                    </thead>
                                    <tbody id="brand" style="border-style: none;">
                                        @if(count($brand_get) == 0)
                                        <tr>
                                            <td colspan="5" style="text-align: center;">
                                            データがありません。
                                            </td>
                                        </tr>
                                        @endif
                                        @foreach($brand_get as $c)
                                        <tr id="brand_{{$c->id}}">
                                            <td >{{$c->brand}}</td>
                                            <td>
                                                {{ $categroy = App\Models\Category::find($c->category_id)->category}}
                                            </td>
                                            <td><img style="width:2em;height:2.2em" src="{{$c->brand_img}}" alt="{{$c->brand}} IMG"></td>
                                            <td><a class="edit" data-brand="{{$c}}" data-bs-toggle="modal" style="cursor:pointer" data-bs-target="#staticBackdrop" id="{{$c->id}}"><i class="bi bi-pencil-square"></i></a></td>
                                            <td><a id="{{$c->id}}" class="del"><i class="bi bi-trash" style="color:red;cursor:pointer"></i></a></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @if (count($brand_get)) {{ $brand_get->onEachSide(1)->links('mypage.pagination') }} @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header  bg-info">
            <h1 class="modal-title fs-5" id="staticBackdropLabel">ブランド</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-12">
                    <select name="" id="category_select" class="form-select my-2">
                        @foreach(App\Models\Category::get() as $category)
                            <option value="{{ $category['id'] }}">{{ $category->category }}</option>
                        @endforeach
                    </select>
                    <input type="text" class="form-control my-2" id="brand_name" name="brand" placeholder="ブランド" />
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <label class="input-group-text" style="height: 18em;cursor:pointer;text-align:center" for="inputGroupFile01">
                        画像のアップロード
                        <span style="color:#838383" class="mx-4"></span>
                        <div class="spinner-border" role="status" style="font-size: 3px;display:none">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </label>
                    <input type="file" accept="image/*" class="form-control" style="visibility: hidden;" id="inputGroupFile01" onchange="uploadImage(event)">
                </div>
                <div class="col-6" id="image_previews" style="border-style: 1px solid #ddd;">
                    <i class="bi bi-images" style="font-size:11em;" id="example_img"></i>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
            <button type="button" id="brand_save" data-bs-dismiss="modal" class="btn btn-primary">保存</button>
        </div>
    </div>
  </div>
</div>
@endsection
@section('add_js')
<script>
    var id = 0;
    $('.edit').on('click', function() {
        id = $(this).attr('id');
    });
    $('#staticBackdrop').on('shown.bs.modal', function(e) {
        console.log('as');
        if (e.relatedTarget.dataset.brand !== undefined) {
            let brandData = JSON.parse(e.relatedTarget.dataset.brand);
            $('#brand_name').val(brandData.brand);
            $('#category_select').val(brandData.category_id);
            if(brandData.brand_img != '' && brandData.brand_img != null && $('textarea[name="product_img_1"]').val() == undefined) {
                let image = `<div id="_product_img_1" class="" style="width:90%;max-height:18em;padding:50px auto" ><img src="`+brandData.brand_img+`" style="width:100%;height:80%" name="img_url" class="img-fluid" /><button type="button" style="width:100%;border-radius:0" onclick="$('#product_img_1').remove();$('#example_img').css('display','block');$('#_product_img_1').remove();" class="btn btn-secondary">削除</button></div>`;
                image += `<textarea style="display: none;" id="product_img_1" class="form-control" name="product_img_1" >`+brandData.brand_img+`</textarea>`;
                $('#example_img').css('display','none');
                $('#image_previews').append(image);
            }
        } else {
            $('#brand_name').val('');
            
        }
    }).on('hidden.bs.modal', function(e) {
        $('#brand_name').val('');
        $('#product_img_1').remove();
        $('#example_img').css('display','block');
        $('#_product_img_1').remove();
    });

    $('#brand_save').on('click', function() {
        if($('#brand_name').val() == '') {
            alert('正しい資料を入力してください。')
        }else {
            if (id > 0) {
                $.ajax({
                    url: "{{ url('/admin/brand/update') }}",
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        id: id,
                        category_id:$('#category_select').val(),
                        brand:$('#brand_name').val(),
                        brand_img:($('#product_img_1').val() == undefined) ? '' : $('#product_img_1').val()
                    },
                    success: function() {
                        id = 0;
                        setTimeout(() => {
                            location.href = window.location.href;
                        }, 10);
                    }
                });
            } else {
                $.ajax({
                    url: "{{ url('/admin/brand') }}",
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        category_id:$('#category_select').val(),
                        brand:$('#brand_name').val(),
                        brand_img:($('#product_img_1').val() == undefined) ? '' : $('#product_img_1').val()
                    },
                    success: function(res) {
                        setTimeout(() => {
                            location.href = window.location.href;
                        }, 10);
                    }
                });
            }
        }
    });

    $('.del').on('click', function() {
        if (!window.confirm('データを本当に削除しますか？')) {
            return;
        }
        let id = $(this).attr('id');
        $.ajax({
            url: "{{ url('/admin/brand/del') }}",
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id:id
            },
            success: function(res) {
                setTimeout(() => {
                    location.href = window.location.href;
                }, 10);
            }
        });
    
    });

    const convertBase64 = (file) => {
        return new Promise((resolve, reject) => {
            const fileReader = new FileReader();
            fileReader.readAsDataURL(file);

            fileReader.onload = () => {
                resolve(fileReader.result);
            };

            fileReader.onerror = (error) => {
                reject(error);
            };
        });
    };

    const uploadImage = async (event) => {
        $('.spinner-border').css('display','block');
        const file = event.target.files[0];
        if (file == undefined && file == null) {
            $('.spinner-border').css('display','none');
            return;
        }
        const base64 = await convertBase64(file);
        // let x = Math.floor((Math.random() * 10000) + 1);
        for (let index = 1; index < 2; index++) {
            let product_image = $('textarea[name="product_img_'+index+'"]').val();
            if(product_image == null && product_image == undefined) {
                let image = `<div id="_product_img_`+index+`" class="" style="width:90%;max-height:18em;padding:50px auto" ><img src="`+base64+`" style="width:100%;height:80%" name="img_url" class="img-fluid" /><button type="button" style="width:100%;border-radius:0" onclick="$('#product_img_`+index+`').remove();$('#example_img').css('display','block');$('#_product_img_`+index+`').remove();" class="btn btn-secondary">削除</button></div>`;
                image += `<textarea style="display: none;" id="product_img_`+index+`" class="form-control" name="product_img_`+index+`" >`+base64+`</textarea>`;
                $('#example_img').css('display','none');
                $('#image_previews').append(image);
                break;
            }
        }
        $('.spinner-border').css('display','none');
    };
</script>
@endsection