  <ol class="breadcrumb mt-1">
    <?php
      $cats = Categories::getPath($params['cat']);
      $numItems = count($cats);
      $i = 0;
    ?>

    @foreach ($cats as $key => $cat)
      @if(++$i === $numItems)
        <li class="breadcrumb-item active">{{$cat['name']}}</li>
      @else
        <li class="breadcrumb-item"><a href="{{action($params['controllerName'].'@index')."?cat=".$cat['id']}}">{{$cat['name']}}</a></li>
      @endif
    @endforeach
  </ol>
