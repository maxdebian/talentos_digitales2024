@extends('layouts.app')
@extends('layouts.menu')
@section('content')


    <div class="row rowFixed">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title titleModule">Product List</h3> <a href="{{ route('product.create') }}" class="btn float-right colorCyan" role="button">+ Add Product</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="listProduct" class="table table-bordered table-striped">

                        <thead>
                            <tr>
                                <th style="width:30%; text-align:center">Description</th>
                                <th style="width:10%; text-align:center">Cost Price</th>
                                <th style="width:30%; text-align:center">Provider</th>
                                <th style="width:20%; text-align:center">Stock</th>
                                <th style="text-align:center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr id='productId_{{$product->id}}'>
                                    <td>
                                        <span class="textFirstName"> {{ $product->description }}</span>
                                    </td>
                                    <td style=" text-align:center">
                                        <span class="textFirstName">{{ !empty($product->cost_price) ? $product->cost_price : '0.00' }}</span>
                                    </td>
                                    <td style=" text-align:center">
                                        <span class="textFirstName">
                                            @if (!empty($product->product_provider))
                                                @foreach ($product->product_provider as $key => $provider)
                                                    {{ $provider->first_name.', '.$provider->last_name }}
                                                    @if ($key < (count($product->product_provider) -1))
                                                        <br>
                                                    @endif
                                                @endforeach
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </td>
                                    <td style=" text-align:center">
                                        <span class="textFirstName">{{ !empty($product->stock) ? $product->stock : '0.00' }}</span>
                                    </td>

                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <button type="button" class="btn paddBto" data-user="{{$product}}" data-toggle="modal" data-target="#showUser-{{$product->id}}" data-title="View">
                                                <svg width="1.2em" height="1.2em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="#4099D4" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z" />
                                                    <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z" />
                                                </svg>
                                            </button>
                                            <form action="{{$product->id}}/edit" method="GET">
                                                <button type="submit" class="btn" data-title="Edit">
                                                    <i class="far fa-edit"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn paddBto" onclick="deleteUser({{$product->id}})" data-title="Delete">
                                                <i class="fas fa-trash-alt" style="color:red"></i>
                                            </button>

                                        </div>
                                    </td>
                                </tr>
                                @include('product/partials/actions')
                            @endforeach
                        </tbody>
                        <tfoot>
                            <thead>
                                <tr>
                                    <th style="width:30%; text-align:center">Description</th>
                                    <th style="width:10%; text-align:center">Cost Price</th>
                                    <th style="width:30%; text-align:center">Provider</th>
                                    <th style="width:10%; text-align:center">Stock</th>
                                    <th style="text-align:center">Actions</th>
                                </tr>
                            </thead>
                        </tfoot>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->





@endsection

@section('scripts')

    <script src="{{ asset('js/modules/product/list.js') }}"></script>

@endsection
