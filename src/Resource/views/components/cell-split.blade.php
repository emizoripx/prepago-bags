@props(['number', 'prorated'])
<div>
    @if ($number > 0)
        <td align="center" class="split">
            <div class="content-top">{{ $number }}</div>
            <div class="content-bottom">
                <small>Excedente</small> <br>
                <small> cu/Bs <span class="font-weight-bold">{{ $prorated }}</span></small>
            </div>
        </td>
    @else
        <td align="center">
            <div>{{ $number == 0 ? 'Ilimitado' : $number }}</div>
        </td>
    @endif
</div>
