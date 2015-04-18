
    @foreach ($callflow as $channel)
      <tr 
      @if($channel->callstate == 'DOWN')
        class="error"
      @elseif($channel->callstate == 'EARLY')
        class="warning"
      @elseif($channel->callstate == 'ACTIVE')
        class="success"
      @endif
      >
      <td>
      {{ $channel->created }}
      </td>
      <td>
      {{ $channel->ip_addr }}
      </td>
      <td>
      {{ $channel->cid_num }}
      </td>
      <td>
      {{ $channel->dest }}
      </td>
      <td>
      {{ $channel->name }}
      </td>
      <td>
      {{ $channel->callstate }}
      </td>
      <td>
      {{ $channel->read_codec }}
      </td>
      </tr>
    @endforeach
