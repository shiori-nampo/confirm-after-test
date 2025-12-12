@extends('layouts.app')


@section('css')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}"/>
@endsection

@section('link')
<form action="/logout" method="post">
  @csrf
<button class="header__link" type="submit">logout</button>
</form>
@endsection

@section('content')
<div class="admin">
  <h2 class="admin__heading content__heading">Admin</h2>
    <div class="admin__inner">
      <form class="search-form" action="/search" method="get">
        @csrf
        <input class="search-form__keyword-input" type="text" name="keyword" placeholder="名前やメールアドレスを入力してください" value="{{request('keyword')}}">
        <div class="search-form__gender">
          <select class="search-form__gender-select" name="gender" value="{{request('gender')}}">
            <option disabled selected>性別</option>
            <option value="1" {{ request('gender') == '1' ? 'selected' : '' }}>男性</option>
            <option value="2" {{ request('gender') == '2' ? 'selected' : '' }}>女性</option>
            <option value="3" {{ request('gender') == '3' ? 'selected' : '' }}>その他</option>
          </select>
        </div>
        <div class="search-form__category">
          <select class="search-form__category-select" name="category_id">
            <option disabled selected>お問い合わせの種類</option>
            @foreach ($categories as $category)
            <option value="{{ $category->id }}" @if( request('category_id')==$category->id ) selected @endif >{{ $category->content }}</option>
            @endforeach
          </select>
        </div>
        <input class="search-form__date" type="date" name="date" value="{{ request('date') }}">
        <div class="search-form__actions">
          <button class="search-form__search-btn btn" type="submit">検索</button>
          <button class="search-form__reset-btn btn" type="submit" name="reset">リセット
          </button>
        </div>
      </form>
      <div class="export-form">
      <form action="{{'/export?'.http_build_query(request()->query())}}" method="post">
        <button class="export__btn btn" type="submit">エクスポート</button>
      </form>
      {{ $contacts->appends(request()->query())->links('vendor.pagination.custom') }}
      </div>
      <table class="admin__table">
      <tr class="admin__row">
        <th class="admin__label">お名前</th>
        <th class="admin__label">性別</th>
        <th class="admin__label">メールアドレス</th>
        <th class="admin__label">お問い合わせの種類</th>
        <th class="admin__label"></th>
      </tr>
      @foreach($contacts as $contact)
      <tr class="admin__row">
        <td class="admin__data">{{ $contact->first_name }} {{ $contact->last_name }}</td>
        <td class="admin__data">
          @if($contact->gender == 1)
          男性
          @elseif($contact->gender == 2)
          女性
          @else
          その他
          @endif</td>
        <td class="admin__data">{{ $contact->email }}</td>
        <td class="admin__data">{{$contact->category->content}}</td>
        <td class="admin__data">
          <a class="admin__detail-btn" href="#{{$contact->id}}">詳細</a>
        </td>
      </tr>
      @endforeach
      </table>

      @foreach($contacts as $contact)
      <div class="modal" id="{{$contact->id}}">
        <a href="#" class="modal-overlay"></a>
        <div class="modal__inner">
          <div class="modal__content">
            <form class="modal__detail-form" action="/delete" method="post">
              @csrf
              @method('DELETE')
              <div class="modal-form__group">
                <label class="modal-form__label">お名前</label>
                <p>{{$contact->first_name}}{{$contact->last_name}}</p>
              </div>

              <div class="modal-form__group">
                <label class="modal-form__label">性別</label>
                <p>@if($contact->gender == 1)
                  男性
                  @elseif($contact->gender == 2)
                  女性
                  @else
                  その他
                  @endif</p>
                </div>

                <div class="modal-form__group">
                <label class="modal-form__label">メールアドレス</label>
                <p>{{$contact->email}}</p>
              </div>

              <div class="modal-form__group">
                <label class="modal-form__label">電話番号</label>
                <p>{{$contact->tell}}</p>
              </div>

              <div class="modal-form__group">
                <label class="modal-form__label">住所</label>
                <p>{{$contact->address}}</p>
              </div>

              <div class="modal-form__group">
                <label class="modal-form__label">お問い合わせの種類
                </label>
                <p>{{$contact->category->content}}</p>
              </div>

              <div class="modal-form__group">
                <label class="modal-form__label">お問い合わせ内容</label>
                <p>{{$contact->detail}}</p>
              </div>
              <input type="hidden" name="id" value="{{ $contact->id }}">
              <button class="modal-form__delete-btn btn" type="submit">削除</button>

            </form>
        </div>

        <a href="#" class="modal__close-btn">x</a>
      </div>
    </div>
    @endforeach
    
  </div>
</div>

</div>
@endsection